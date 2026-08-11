<?php
namespace App\Models;

use App\Core\Model;

/**
 * 訂位明細：一位賓客在一個「競賽日 + 場次」的一筆訂位。
 *
 * 狀態流程（見規格 Figure 1）：
 *   new -> requested -> confirmed / waitlisted
 *                    -> declined
 *                    -> reschedule（仍為 requested，改期後再確認）
 */
class Reservation extends Model
{
    /** @var string[] 允許的狀態 */
    public const STATUSES = ['requested', 'confirmed', 'waitlisted', 'declined'];

    /** @var array<string,int> 管理頁排序用的狀態權重 */
    private const STATUS_ORDER = [
        'confirmed'  => 1,
        'requested'  => 2,
        'waitlisted' => 3,
        'declined'   => 4,
    ];

    /**
     * 佔用座位統計：confirmed 與 requested 都視為已佔位
     *
     * @return array<int, array<int, int>> [競賽日 id][場次 id] => 佔位數
     */
    public function occupancyMap(): array
    {
        $rows = $this->db->fetchAll(
            "SELECT competition_day_id, seating_id, COUNT(*) AS used
               FROM reservation
              WHERE status IN ('confirmed', 'requested')
              GROUP BY competition_day_id, seating_id"
        );

        $map = [];

        foreach ($rows as $row) {
            $map[(int) $row['competition_day_id']][(int) $row['seating_id']] = (int) $row['used'];
        }

        return $map;
    }

    /**
     * 產生剩餘座位表
     *
     * @param array $days     競賽日清單
     * @param array $seatings 場次清單（需含 total_seats）
     * @return array<int, array<int, int>> [競賽日 id][場次 id] => 剩餘座位數
     */
    public function availabilityMap(array $days, array $seatings): array
    {
        $occupancy    = $this->occupancyMap();
        $availability = [];

        foreach ($days as $day) {
            $dayId = (int) $day['id'];

            foreach ($seatings as $seating) {
                $seatingId = (int) $seating['id'];
                $used      = $occupancy[$dayId][$seatingId] ?? 0;

                $availability[$dayId][$seatingId] = max(0, (int) $seating['total_seats'] - $used);
            }
        }

        return $availability;
    }

    /**
     * 各國家在各場次已使用的名額（declined 不計）
     *
     * @return array<int, array<int, array<string, int>>> [日][場次][國家] => 數量
     */
    public function countryUsageMap(): array
    {
        $rows = $this->db->fetchAll(
            "SELECT competition_day_id, seating_id, guest_country, COUNT(*) AS used
               FROM reservation
              WHERE status <> 'declined'
              GROUP BY competition_day_id, seating_id, guest_country"
        );

        $map = [];

        foreach ($rows as $row) {
            $map[(int) $row['competition_day_id']][(int) $row['seating_id']][$row['guest_country']]
                = (int) $row['used'];
        }

        return $map;
    }

    /**
     * 新增一筆訂位
     */
    public function create(int $bookingId, int $dayId, int $seatingId, ?string $guestName, string $country, string $status): int
    {
        $this->db->run(
            'INSERT INTO reservation
                (booking_id, competition_day_id, seating_id, guest_name, guest_country, status)
             VALUES (?, ?, ?, ?, ?, ?)',
            [$bookingId, $dayId, $seatingId, $guestName, $country, $status]
        );

        return $this->db->lastInsertId();
    }

    /**
     * 取得某筆訂位申請的所有賓客（依日、場次排序），供送出確認頁使用
     */
    public function forBooking(int $bookingId): array
    {
        return $this->db->fetchAll(
            'SELECT r.id, r.guest_name, r.guest_country, r.status,
                    d.code AS day_code, d.day_date, d.sort_order AS day_order,
                    s.id AS seating_id, s.start_time, s.end_time, s.sort_order AS seating_order,
                    m.name AS module_name
               FROM reservation r
               INNER JOIN competition_day d ON d.id = r.competition_day_id
               INNER JOIN seating s        ON s.id = r.seating_id
               INNER JOIN dining_module m  ON m.id = s.dining_module_id
              WHERE r.booking_id = ?
              ORDER BY d.sort_order, s.sort_order, r.id',
            [$bookingId]
        );
    }

    /**
     * 管理頁清單：依 日 -> 場次 -> 狀態 -> 訂位編號 排序，並加上每個場次的流水編號
     */
    public function managementList(): array
    {
        $rows = $this->db->fetchAll(
            'SELECT r.id, r.booking_id, r.competition_day_id, r.seating_id,
                    r.guest_name, r.guest_country, r.status, r.needs_reschedule,
                    b.booking_no,
                    c.name AS contact_name, c.organization, c.email, c.phone,
                    c.country AS contact_country,
                    d.code AS day_code, d.day_date, d.sort_order AS day_order,
                    s.start_time, s.end_time, s.sort_order AS seating_order,
                    m.name AS module_name
               FROM reservation r
               INNER JOIN booking b        ON b.id = r.booking_id
               INNER JOIN booking_contact c ON c.id = b.booking_contact_id
               INNER JOIN competition_day d ON d.id = r.competition_day_id
               INNER JOIN seating s         ON s.id = r.seating_id
               INNER JOIN dining_module m   ON m.id = s.dining_module_id
              ORDER BY d.sort_order,
                       s.sort_order,
                       FIELD(r.status, \'confirmed\', \'requested\', \'waitlisted\', \'declined\'),
                       b.booking_no,
                       r.id'
        );

        // 每個「日 + 場次」重新編號，方便工作人員判斷還能放多少人
        $counters = [];

        foreach ($rows as &$row) {
            $key = $row['competition_day_id'] . '-' . $row['seating_id'];
            $counters[$key] = ($counters[$key] ?? 0) + 1;
            $row['seq']        = $counters[$key];
            $row['time_label'] = substr($row['start_time'], 0, 5) . ' - ' . substr($row['end_time'], 0, 5);
        }
        unset($row);

        return $rows;
    }

    /**
     * 取得目前仍為 requested（可被工作人員處理）的訂位 id
     *
     * @return int[]
     */
    public function pendingIds(): array
    {
        $rows = $this->db->fetchAll("SELECT id FROM reservation WHERE status = 'requested'");

        return array_map(static fn (array $row): int => (int) $row['id'], $rows);
    }

    /**
     * 更新狀態（僅允許合法狀態）
     */
    public function updateStatus(int $id, string $status): void
    {
        if (!in_array($status, self::STATUSES, true)) {
            throw new \InvalidArgumentException('不合法的訂位狀態：' . $status);
        }

        $this->db->run(
            'UPDATE reservation SET status = ?, needs_reschedule = 0 WHERE id = ?',
            [$status, $id]
        );
    }

    /**
     * 標記為待改期（狀態維持 requested，管理頁下次載入會顯示日期／場次下拉選單）
     */
    public function markForReschedule(int $id): void
    {
        $this->db->run(
            "UPDATE reservation SET status = 'requested', needs_reschedule = 1 WHERE id = ?",
            [$id]
        );
    }

    /**
     * 改期：更新競賽日與場次
     */
    public function reschedule(int $id, int $dayId, int $seatingId): void
    {
        $this->db->run(
            'UPDATE reservation SET competition_day_id = ?, seating_id = ? WHERE id = ?',
            [$dayId, $seatingId, $id]
        );
    }

    /**
     * 匯出給餐廳外場主管的賓客名單：僅 confirmed，依 日 -> 場次 -> 訂位編號 排序
     */
    public function guestList(): array
    {
        return $this->db->fetchAll(
            "SELECT b.booking_no,
                    c.name AS contact_name,
                    c.organization,
                    r.guest_name,
                    r.guest_country,
                    d.code AS day_code, d.day_date,
                    s.start_time, s.end_time,
                    m.name AS module_name
               FROM reservation r
               INNER JOIN booking b         ON b.id = r.booking_id
               INNER JOIN booking_contact c ON c.id = b.booking_contact_id
               INNER JOIN competition_day d ON d.id = r.competition_day_id
               INNER JOIN seating s         ON s.id = r.seating_id
               INNER JOIN dining_module m   ON m.id = s.dining_module_id
              WHERE r.status = 'confirmed'
              ORDER BY d.sort_order, s.sort_order, b.booking_no, r.id"
        );
    }

    /**
     * 取得每位聯絡人的訂位狀態明細，供「Send emails」產生通知檔
     */
    public function byContact(): array
    {
        $rows = $this->db->fetchAll(
            'SELECT c.id AS contact_id, c.name AS contact_name, c.organization,
                    c.email, c.phone, c.notified_at,
                    b.booking_no,
                    r.guest_name, r.guest_country, r.status,
                    d.code AS day_code, d.day_date,
                    s.start_time, s.end_time, s.sort_order AS seating_order,
                    d.sort_order AS day_order,
                    m.name AS module_name
               FROM reservation r
               INNER JOIN booking b         ON b.id = r.booking_id
               INNER JOIN booking_contact c ON c.id = b.booking_contact_id
               INNER JOIN competition_day d ON d.id = r.competition_day_id
               INNER JOIN seating s         ON s.id = r.seating_id
               INNER JOIN dining_module m   ON m.id = s.dining_module_id
              ORDER BY c.id, d.sort_order, s.sort_order, b.booking_no, r.id'
        );

        $contacts = [];

        foreach ($rows as $row) {
            $contactId = (int) $row['contact_id'];

            if (!isset($contacts[$contactId])) {
                $contacts[$contactId] = [
                    'id'           => $contactId,
                    'name'         => $row['contact_name'],
                    'organization' => $row['organization'],
                    'email'        => $row['email'],
                    'phone'        => $row['phone'],
                    'notified_at'  => $row['notified_at'],
                    'reservations' => [],
                ];
            }

            $contacts[$contactId]['reservations'][] = $row;
        }

        return $contacts;
    }
}
