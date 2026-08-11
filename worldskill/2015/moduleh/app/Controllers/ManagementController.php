<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Countries;
use App\Core\Url;
use App\Models\CompetitionDay;
use App\Models\Reservation;
use App\Models\Seating;
use App\Services\EmailService;

/**
 * WSI 工作人員的訂位管理功能。
 * 進入點為實體檔案 management/ReservationManagement.php 與 management/GuestList.php。
 */
class ManagementController extends Controller
{
    /** @var array 應用程式設定 */
    private array $config;

    /**
     * 設定注入（由管理區進入點傳入 config）
     */
    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    /**
     * 訂位管理主畫面
     */
    public function index(): string
    {
        $reservationModel = new Reservation();
        $messages         = [];

        if ($this->request->isPost()) {
            if ($this->request->hasPost('send-emails')) {
                $messages[] = $this->sendEmails();
            } else {
                $messages[] = $this->saveChanges($reservationModel);
            }
        }

        $dayModel     = new CompetitionDay();
        $seatingModel = new Seating();

        return $this->render('management', [
            'pageTitle'    => 'Reservation management',
            'breadcrumb'   => ['Management'],
            'reservations' => $reservationModel->managementList(),
            'days'         => $dayModel->all(),
            'seatings'     => $seatingModel->all(),
            'messages'     => $messages,
        ]);
    }

    /**
     * 儲存工作人員在清單上做的決定
     */
    private function saveChanges(Reservation $reservationModel): string
    {
        $actions   = $this->request->postArray('action');
        $days      = $this->request->postArray('reschedule_day');
        $seatings  = $this->request->postArray('reschedule_seating');
        $changed   = 0;

        // 只允許處理目前仍為 requested 狀態的項目（畫面上也只有這些會顯示單選鈕）
        $pending = $reservationModel->pendingIds();

        // 先處理改期下拉選單（針對先前已標記為 reschedule 的項目）
        foreach ($days as $reservationId => $dayId) {
            $reservationId = (int) $reservationId;
            $dayId         = (int) $dayId;
            $seatingId     = (int) ($seatings[$reservationId] ?? 0);

            if ($reservationId > 0 && $dayId > 0 && $seatingId > 0
                && in_array($reservationId, $pending, true)) {
                $reservationModel->reschedule($reservationId, $dayId, $seatingId);
                $changed++;
            }
        }

        // 再處理狀態單選鈕
        foreach ($actions as $reservationId => $action) {
            $reservationId = (int) $reservationId;

            if ($reservationId <= 0 || !in_array($reservationId, $pending, true)) {
                continue;
            }

            switch ($action) {
                case 'confirm':
                    $reservationModel->updateStatus($reservationId, 'confirmed');
                    $changed++;
                    break;
                case 'decline':
                    $reservationModel->updateStatus($reservationId, 'declined');
                    $changed++;
                    break;
                case 'waitlist':
                    $reservationModel->updateStatus($reservationId, 'waitlisted');
                    $changed++;
                    break;
                case 'reschedule':
                    $reservationModel->markForReschedule($reservationId);
                    $changed++;
                    break;
                default:
                    // 未做決定的項目保持原狀
                    break;
            }
        }

        return $changed > 0
            ? sprintf('%d entr%s updated.', $changed, $changed === 1 ? 'y' : 'ies')
            : 'Nothing to save - no decision was selected.';
    }

    /**
     * 「Send emails」：為每位聯絡人在 /emails 目錄產生一個文字檔
     */
    private function sendEmails(): string
    {
        $service = new EmailService($this->config['app']['email_dir']);
        $result  = $service->sendAll();

        return sprintf(
            '%d notification file(s) written to /emails, %d contact(s) skipped (already final and notified).',
            count($result['written']),
            $result['skipped']
        );
    }

    /**
     * 賓客名單（畫面）：僅 confirmed，依 日 -> 場次 分組，並依訂位編號排序
     */
    public function guestList(): string
    {
        $rows   = (new Reservation())->guestList();
        $groups = [];

        foreach ($rows as $row) {
            $key = sprintf(
                '%s - %s | %s %s - %s',
                $row['day_code'],
                date('d.m.Y', strtotime($row['day_date'])),
                $row['module_name'],
                substr($row['start_time'], 0, 5),
                substr($row['end_time'], 0, 5)
            );

            $groups[$key][] = $row;
        }

        return $this->render('guestlist', [
            'pageTitle'  => 'Guest list for the Restaurant Service host',
            'breadcrumb' => ['Management', 'Guest list'],
            'groups'     => $groups,
            'csvUrl'     => Url::managementPage('GuestList.php', 'format=csv'),
        ]);
    }

    /**
     * 賓客名單（CSV 下載）
     */
    public function guestListCsv(): void
    {
        $rows = (new Reservation())->guestList();

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="guest-list.csv"');

        $output = fopen('php://output', 'wb');

        // 加上 BOM，讓 Excel 正確辨識 UTF-8
        fwrite($output, "\xEF\xBB\xBF");

        fputcsv($output, [
            'Day', 'Date', 'Seating', 'Booking No',
            'Booking Contact Name', 'Booking Contact Organization',
            'Guest Name', 'Guest Country',
        ]);

        foreach ($rows as $row) {
            fputcsv($output, [
                $row['day_code'],
                date('d.m.Y', strtotime($row['day_date'])),
                $row['module_name'] . ' ' . substr($row['start_time'], 0, 5)
                    . ' - ' . substr($row['end_time'], 0, 5),
                $row['booking_no'],
                $row['contact_name'],
                $row['organization'] ?? '',
                $row['guest_name'] ?? '',
                $row['guest_country'] . ' - ' . Countries::name($row['guest_country']),
            ]);
        }

        fclose($output);
        exit;
    }
}
