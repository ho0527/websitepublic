<?php
namespace App\Models;

use App\Core\Model;

/**
 * 一次送出的訂位申請，對外以 Booking No（例如 201500021）表示。
 */
class Booking extends Model
{
    /** @var string 訂位編號前綴 */
    private string $prefix;

    public function __construct(string $prefix = '2015')
    {
        parent::__construct();
        $this->prefix = $prefix;
    }

    /**
     * 建立訂位申請主檔並產生訂位編號
     *
     * @param int    $contactId 聯絡人 id
     * @param string $type      individual | group
     * @return array{id:int, booking_no:string}
     */
    public function create(int $contactId, string $type): array
    {
        // 先以暫時值插入取得自動編號，再依 id 產生正式訂位編號
        $temporaryNo = 'TMP-' . uniqid('', true);

        $this->db->run(
            'INSERT INTO booking (booking_no, booking_contact_id, booking_type)
             VALUES (?, ?, ?)',
            [$temporaryNo, $contactId, $type]
        );

        $id        = $this->db->lastInsertId();
        $bookingNo = $this->prefix . str_pad((string) $id, 5, '0', STR_PAD_LEFT);

        $this->db->run('UPDATE booking SET booking_no = ? WHERE id = ?', [$bookingNo, $id]);

        return ['id' => $id, 'booking_no' => $bookingNo];
    }

    /**
     * 依 id 取得訂位申請（含聯絡人資料）
     */
    public function findWithContact(int $id): ?array
    {
        return $this->db->fetchOne(
            'SELECT b.id, b.booking_no, b.booking_type, b.created_at,
                    c.id AS contact_id, c.name AS contact_name, c.organization,
                    c.email, c.phone, c.country AS contact_country
               FROM booking b
               INNER JOIN booking_contact c ON c.id = b.booking_contact_id
              WHERE b.id = ?',
            [$id]
        );
    }
}
