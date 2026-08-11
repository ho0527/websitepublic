<?php
namespace App\Services;

use App\Core\Countries;
use App\Core\Database;
use App\Models\BookingContact;
use App\Models\Reservation;

/**
 * 通知信服務。
 *
 * 依規格：競賽階段「Send emails」不真的寄信，而是把內容存成文字檔放到 /emails 目錄。
 * 規則：重複按下按鈕會重複產生通知檔，除非該聯絡人的每一位賓客都已是
 *       confirmed 或 declined（狀態已定案且先前已通知過）。
 */
class EmailService
{
    /** @var string 通知檔存放目錄 */
    private string $emailDir;

    private Reservation $reservationModel;
    private BookingContact $contactModel;

    public function __construct(string $emailDir)
    {
        $this->emailDir         = rtrim($emailDir, '/\\');
        $this->reservationModel = new Reservation();
        $this->contactModel     = new BookingContact();

        if (!is_dir($this->emailDir)) {
            mkdir($this->emailDir, 0777, true);
        }
    }

    /**
     * 產生所有應通知聯絡人的文字檔
     *
     * @return array{written: string[], skipped: int}
     */
    public function sendAll(): array
    {
        $contacts = $this->reservationModel->byContact();
        $written  = [];
        $skipped  = 0;

        foreach ($contacts as $contact) {
            if (!$this->shouldNotify($contact)) {
                $skipped++;
                continue;
            }

            $written[] = $this->writeFile($contact);
            $this->contactModel->markNotified($contact['id']);
        }

        return ['written' => $written, 'skipped' => $skipped];
    }

    /**
     * 判斷是否需要（再次）通知：
     * 若所有賓客都已 confirmed / declined 且已通知過，就不再重複產生。
     */
    private function shouldNotify(array $contact): bool
    {
        if ($contact['notified_at'] === null) {
            return true;
        }

        foreach ($contact['reservations'] as $reservation) {
            if (!in_array($reservation['status'], ['confirmed', 'declined'], true)) {
                // 仍有未定案的賓客 -> 每次都要再通知一次
                return true;
            }
        }

        return false;
    }

    /**
     * 實際寫出文字檔，內容依「競賽日 + 場次」分組
     */
    private function writeFile(array $contact): string
    {
        $fileName = sprintf(
            '%s_contact-%d_%s.txt',
            date('Ymd-His'),
            $contact['id'],
            preg_replace('/[^a-zA-Z0-9._-]/', '-', (string) $contact['email'])
        );

        $lines   = [];
        $lines[] = 'To: ' . $contact['email'];
        $lines[] = 'Subject: WorldSkills Restaurant Service - status of your booking request(s)';
        $lines[] = str_repeat('=', 78);
        $lines[] = '';
        $lines[] = 'Dear ' . $contact['name'] . ',';
        $lines[] = '';
        $lines[] = 'Below you find the current status of every guest of your booking request(s):';
        $lines[] = '';

        $currentGroup = null;

        foreach ($contact['reservations'] as $reservation) {
            $group = sprintf(
                '%s - %s, %s %s - %s',
                $reservation['day_code'],
                date('d.m.Y', strtotime($reservation['day_date'])),
                $reservation['module_name'],
                substr($reservation['start_time'], 0, 5),
                substr($reservation['end_time'], 0, 5)
            );

            if ($group !== $currentGroup) {
                $currentGroup = $group;
                $lines[]      = $group;
            }

            $lines[] = sprintf(
                '    [%-10s] %s (%s) - booking no %s',
                $reservation['status'],
                $reservation['guest_name'] !== null && $reservation['guest_name'] !== ''
                    ? $reservation['guest_name']
                    : '(name not given)',
                Countries::name($reservation['guest_country']),
                $reservation['booking_no']
            );
        }

        $lines[] = '';
        $lines[] = 'Guests on the waiting list will be called as soon as a cancellation occurs.';
        $lines[] = '';
        $lines[] = 'Kind regards,';
        $lines[] = 'WorldSkills International - Restaurant Service';
        $lines[] = 'Generated at ' . date('Y-m-d H:i:s');

        file_put_contents(
            $this->emailDir . DIRECTORY_SEPARATOR . $fileName,
            implode(PHP_EOL, $lines) . PHP_EOL
        );

        Database::getInstance()->run(
            'INSERT INTO email_log (booking_contact_id, file_name) VALUES (?, ?)',
            [$contact['id'], $fileName]
        );

        return $fileName;
    }
}
