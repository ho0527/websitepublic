<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Config;
use App\Core\Database;
use App\Models\Booking;
use App\Models\Station;
use App\Models\Train;

/**
 * 訂票流程。
 *
 * 集中處理訂票的所有檢核與建立，控制器只負責收集輸入與呈現結果。
 */
final class BookingService
{
    private ScheduleService $scheduleService;

    private SeatService $seatService;

    private SmsService $smsService;

    private CaptchaService $captchaService;

    private BookingCodeGenerator $codeGenerator;

    public function __construct(
        ScheduleService $scheduleService,
        SeatService $seatService,
        SmsService $smsService,
        CaptchaService $captchaService,
        BookingCodeGenerator $codeGenerator
    ) {
        $this->scheduleService = $scheduleService;
        $this->seatService     = $seatService;
        $this->smsService      = $smsService;
        $this->captchaService  = $captchaService;
        $this->codeGenerator   = $codeGenerator;
    }

    /**
     * 依使用者輸入建立訂票。
     *
     * @param array{
     *     phone: string,
     *     train_code: string,
     *     from_station: string,
     *     to_station: string,
     *     travel_date: string,
     *     ticket_count: string
     * } $input
     *
     * @return array{booking: Booking|null, errors: array<int, string>}
     */
    public function book(array $input, \DateTimeInterface $now): array
    {
        $errors = $this->validateRequiredFields($input);

        if ($errors !== []) {
            return ['booking' => null, 'errors' => $errors];
        }

        // 驗證碼必須在送出訂票前已通過
        if (!$this->captchaService->hasPassed()) {
            return ['booking' => null, 'errors' => ['問答驗證碼尚未通過，請完成驗證後再送出訂票']];
        }

        $ticketCount = (int) $input['ticket_count'];
        $minTickets  = (int) Config::get('booking.min_tickets', 1);
        $maxTickets  = (int) Config::get('booking.max_tickets', 1000);

        if ($ticketCount < $minTickets || $ticketCount > $maxTickets) {
            return [
                'booking' => null,
                'errors'  => [sprintf('車票張數必須介於 %d 至 %d 張之間', $minTickets, $maxTickets)],
            ];
        }

        $fromStation = Station::findByCode($input['from_station']);
        $toStation   = Station::findByCode($input['to_station']);

        if ($fromStation === null || $toStation === null) {
            return ['booking' => null, 'errors' => ['請選擇正確的起程站與到達站']];
        }

        if ($fromStation->id() === $toStation->id()) {
            return ['booking' => null, 'errors' => ['起程站與到達站不可相同，請重新選擇']];
        }

        $travelDate = $this->parseDate($input['travel_date']);

        if ($travelDate === null) {
            return ['booking' => null, 'errors' => ['乘車日期格式不正確']];
        }

        $train = Train::findActiveByCode($input['train_code']);

        if ($train === null) {
            return ['booking' => null, 'errors' => ['查無此車次代碼，請重新選擇']];
        }

        // 該日期是否有這班車
        if (!$train->runsOn($travelDate)) {
            return [
                'booking' => null,
                'errors'  => [sprintf('%s 當日無 %s 車次的列車，請改選其他日期或車次', $travelDate->format('Y/m/d'), $train->code)],
            ];
        }

        // 該列車是否依序行經起訖站
        $segment = $this->scheduleService->segmentOf($train, $fromStation->id(), $toStation->id(), $travelDate);

        if ($segment === null) {
            return [
                'booking' => null,
                'errors'  => [sprintf('%s 車次並未依序行經 %s 站與 %s 站，請重新選擇', $train->code, $fromStation->name, $toStation->name)],
            ];
        }

        // 發車時間是否已過
        if ($segment['depart'] <= $now) {
            return ['booking' => null, 'errors' => ['該班次的發車時間已過，請改訂其他班次']];
        }

        $fromSequence = (int) $train->stopAtStation($fromStation->id())->stop_sequence;
        $toSequence   = (int) $train->stopAtStation($toStation->id())->stop_sequence;

        // 座位檢查與寫入放在同一個交易中，避免同時訂票造成超賣
        return Database::instance()->transaction(function () use (
            $train,
            $travelDate,
            $fromSequence,
            $toSequence,
            $ticketCount,
            $segment,
            $fromStation,
            $toStation,
            $input,
            $now
        ): array {
            $available = $this->seatService->availableSeats($train, $travelDate, $fromSequence, $toSequence);

            if ($available < $ticketCount) {
                return [
                    'booking' => null,
                    'errors'  => [sprintf(
                        '%s 站至 %s 站的區間已無足夠空位（剩餘 %d 個座位），請減少張數或改訂其他班次',
                        $fromStation->name,
                        $toStation->name,
                        $available
                    )],
                ];
            }

            $booking = Booking::create([
                'booking_code'    => $this->codeGenerator->generate(),
                'phone'           => $input['phone'],
                'train_id'        => $train->id(),
                'from_station_id' => $fromStation->id(),
                'to_station_id'   => $toStation->id(),
                'travel_date'     => $travelDate->format('Y-m-d'),
                'depart_at'       => $segment['depart']->format('Y-m-d H:i:s'),
                'arrive_at'       => $segment['arrive']->format('Y-m-d H:i:s'),
                'ticket_count'    => $ticketCount,
                'unit_price'      => $segment['fare'],
                'total_price'     => $segment['fare'] * $ticketCount,
                'status'          => Booking::STATUS_BOOKED,
                'created_at'      => $now->format('Y-m-d H:i:s'),
            ]);

            $this->smsService->sendBookingConfirmed($booking);
            $this->captchaService->consume();

            return ['booking' => $booking, 'errors' => []];
        });
    }

    /**
     * 取消訂票並發送通知簡訊。
     */
    public function cancel(Booking $booking, string $source, \DateTimeInterface $now): void
    {
        $booking->cancel($source, $now);

        if ($source === Booking::CANCELLED_BY_ADMIN) {
            $this->smsService->sendCancelledByAdmin($booking, $now);
        }
    }

    /**
     * 檢查必填欄位。
     *
     * @param array<string, string> $input
     * @return array<int, string>
     */
    private function validateRequiredFields(array $input): array
    {
        $labels = [
            'phone'        => '手機號碼',
            'from_station' => '起程站',
            'to_station'   => '到達站',
            'travel_date'  => '乘車日期',
            'train_code'   => '車次代碼',
            'ticket_count' => '車票張數',
        ];

        $errors = [];

        foreach ($labels as $field => $label) {
            if (trim((string) ($input[$field] ?? '')) === '') {
                $errors[] = sprintf('請填寫%s', $label);
            }
        }

        if ($errors === [] && preg_match('/^09\d{8}$/', $input['phone']) !== 1) {
            $errors[] = '手機號碼格式不正確，請輸入 09 開頭的 10 位數字';
        }

        return $errors;
    }

    /**
     * 解析日期字串，格式不符時回傳 null。
     */
    private function parseDate(string $value): ?\DateTimeImmutable
    {
        $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $value);

        return $date === false ? null : $date;
    }
}
