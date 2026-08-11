<?php
namespace App\Services;

use App\Core\Countries;
use App\Core\Database;
use App\Models\Booking;
use App\Models\BookingContact;
use App\Models\CompetitionDay;
use App\Models\Reservation;
use App\Models\Seating;

/**
 * 訂位流程的商業邏輯：
 *  - 解析表單欄位（沿用官方樣板的 c{n}-d{m}-n{k} / -o{k} 命名）
 *  - 驗證同一國家在同一場次的人數上限
 *  - 依剩餘座位決定 requested 或 waitlisted
 *  - 以交易寫入 booking_contact / booking / reservation
 */
class BookingService
{
    private CompetitionDay $dayModel;
    private Seating $seatingModel;
    private Reservation $reservationModel;
    private BookingContact $contactModel;
    private Booking $bookingModel;

    /** @var array 競賽日清單（依 sort_order） */
    private array $days;

    /** @var array 場次清單（依 sort_order） */
    private array $seatings;

    public function __construct(string $bookingPrefix = '2015')
    {
        $this->dayModel         = new CompetitionDay();
        $this->seatingModel     = new Seating();
        $this->reservationModel = new Reservation();
        $this->contactModel     = new BookingContact();
        $this->bookingModel     = new Booking($bookingPrefix);

        $this->days     = $this->dayModel->all();
        $this->seatings = $this->seatingModel->all();
    }

    public function days(): array
    {
        return $this->days;
    }

    public function seatings(): array
    {
        return $this->seatings;
    }

    /**
     * 剩餘座位表 [競賽日 id][場次 id] => 數量
     */
    public function availability(): array
    {
        return $this->reservationModel->availabilityMap($this->days, $this->seatings);
    }

    /**
     * 表單欄位前綴：第 $dayIndex 天（1 起算）、第 $seatingIndex 個場次（1 起算）
     */
    public static function fieldPrefix(int $dayIndex, int $seatingIndex): string
    {
        return 'c' . $dayIndex . '-d' . $seatingIndex;
    }

    /**
     * 解析個人訂位表單：勾選的核取方塊 => 選擇的場次
     *
     * @return array<int, array{day_index:int, seating_index:int, day:array, seating:array}>
     */
    public function parseIndividualSelection(array $post): array
    {
        $selected = [];

        foreach ($this->days as $dayIndex => $day) {
            foreach ($this->seatings as $seatingIndex => $seating) {
                $field = self::fieldPrefix($dayIndex + 1, $seatingIndex + 1) . '-n1';

                if (!empty($post[$field])) {
                    $selected[] = [
                        'day_index'     => $dayIndex + 1,
                        'seating_index' => $seatingIndex + 1,
                        'day'           => $day,
                        'seating'       => $seating,
                    ];
                }
            }
        }

        return $selected;
    }

    /**
     * 解析團體訂位表單，回傳每個場次的賓客輸入內容（含未選國家者，以便錯誤時回填）
     *
     * @return array<string, array<int, array{name:string, country:string}>>
     *         鍵為「dayIndex-seatingIndex」
     */
    public function parseGroupInput(array $post): array
    {
        $guests = [];

        foreach ($this->days as $dayIndex => $day) {
            foreach ($this->seatings as $seatingIndex => $seating) {
                $prefix = self::fieldPrefix($dayIndex + 1, $seatingIndex + 1);
                $key    = ($dayIndex + 1) . '-' . ($seatingIndex + 1);
                $rows   = [];

                // 逐一往後掃描 n1/o1、n2/o2 …，只要任一欄位存在就視為一列
                for ($i = 1; $i <= 200; $i++) {
                    $nameField    = $prefix . '-n' . $i;
                    $countryField = $prefix . '-o' . $i;

                    if (!isset($post[$nameField]) && !isset($post[$countryField])) {
                        continue;
                    }

                    $rows[] = [
                        'name'    => trim((string) ($post[$nameField] ?? '')),
                        'country' => trim((string) ($post[$countryField] ?? '')),
                    ];
                }

                if ($rows !== []) {
                    $guests[$key] = $rows;
                }
            }
        }

        return $guests;
    }

    /**
     * 驗證團體訂位：同一國家在同一場次不得超過「總座位數 - 每位選手服務座位數」
     *
     * @return string[] 錯誤訊息（每個違規場次一則）
     */
    public function validateGroup(array $guests): array
    {
        $existingUsage = $this->reservationModel->countryUsageMap();
        $errors        = [];

        foreach ($guests as $key => $rows) {
            [$dayIndex, $seatingIndex] = array_map('intval', explode('-', $key));

            $day     = $this->days[$dayIndex - 1]     ?? null;
            $seating = $this->seatings[$seatingIndex - 1] ?? null;

            if ($day === null || $seating === null) {
                continue;
            }

            $dayId     = (int) $day['id'];
            $seatingId = (int) $seating['id'];

            // 統計本次送出的每個國家人數（只有選了國家的才算）
            $requested = [];

            foreach ($rows as $row) {
                if ($row['country'] === '' || !Countries::isValid($row['country'])) {
                    continue;
                }
                $requested[$row['country']] = ($requested[$row['country']] ?? 0) + 1;
            }

            foreach ($requested as $country => $count) {
                $already = $existingUsage[$dayId][$seatingId][$country] ?? 0;

                if ($already + $count > (int) $seating['max_per_country']) {
                    $errors[] = sprintf(
                        'from country %s for %s on %s - %s, %s (maximum %d guests per country, %d already requested)',
                        $country,
                        $seating['module_name'],
                        $day['code'],
                        date('d.m.Y', strtotime($day['day_date'])),
                        $seating['time_label'],
                        (int) $seating['max_per_country'],
                        $already
                    );
                }
            }
        }

        return $errors;
    }

    /**
     * 寫入個人訂位
     *
     * @param array $contact  已驗證的聯絡人資料
     * @param array $selected parseIndividualSelection() 的結果
     * @return array{id:int, booking_no:string}
     */
    public function storeIndividual(array $contact, array $selected): array
    {
        $guestsBySlot = [];

        foreach ($selected as $item) {
            // 個人訂位：聯絡人的姓名與國家即為賓客資料
            $guestsBySlot[] = [
                'day'     => $item['day'],
                'seating' => $item['seating'],
                'guests'  => [[
                    'name'    => $contact['name'],
                    'country' => $contact['country'],
                ]],
            ];
        }

        return $this->persist($contact, 'individual', $guestsBySlot);
    }

    /**
     * 寫入團體訂位（只儲存有選國家的列）
     *
     * @return array{id:int, booking_no:string}
     */
    public function storeGroup(array $contact, array $guests): array
    {
        $guestsBySlot = [];

        foreach ($guests as $key => $rows) {
            [$dayIndex, $seatingIndex] = array_map('intval', explode('-', $key));

            $day     = $this->days[$dayIndex - 1]         ?? null;
            $seating = $this->seatings[$seatingIndex - 1] ?? null;

            if ($day === null || $seating === null) {
                continue;
            }

            $valid = [];

            foreach ($rows as $row) {
                // 規格：只有選了國家的列才會被存成訂位申請
                if ($row['country'] === '' || !Countries::isValid($row['country'])) {
                    continue;
                }

                $valid[] = [
                    'name'    => $row['name'] !== '' ? $row['name'] : null,
                    'country' => $row['country'],
                ];
            }

            if ($valid !== []) {
                $guestsBySlot[] = [
                    'day'     => $day,
                    'seating' => $seating,
                    'guests'  => $valid,
                ];
            }
        }

        return $this->persist($contact, 'group', $guestsBySlot);
    }

    /**
     * 實際寫入資料庫（單一交易），並依剩餘座位決定 requested / waitlisted
     *
     * @param array $guestsBySlot [['day'=>..,'seating'=>..,'guests'=>[['name','country'],..]], ..]
     * @return array{id:int, booking_no:string}
     */
    private function persist(array $contact, string $type, array $guestsBySlot): array
    {
        $pdo          = Database::getInstance()->pdo();
        $availability = $this->availability();

        $pdo->beginTransaction();

        try {
            $contactId = $this->contactModel->create($contact);
            $booking   = $this->bookingModel->create($contactId, $type);

            foreach ($guestsBySlot as $slot) {
                $dayId     = (int) $slot['day']['id'];
                $seatingId = (int) $slot['seating']['id'];

                foreach ($slot['guests'] as $guest) {
                    $free   = $availability[$dayId][$seatingId] ?? 0;
                    $status = $free > 0 ? 'requested' : 'waitlisted';

                    if ($free > 0) {
                        $availability[$dayId][$seatingId] = $free - 1;
                    }

                    $this->reservationModel->create(
                        $booking['id'],
                        $dayId,
                        $seatingId,
                        ($guest['name'] ?? '') !== '' ? $guest['name'] : null,
                        $guest['country'],
                        $status
                    );
                }
            }

            $pdo->commit();
        } catch (\Throwable $exception) {
            $pdo->rollBack();
            throw $exception;
        }

        return $booking;
    }
}
