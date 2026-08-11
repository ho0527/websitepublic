<?php
namespace App\Models;

use App\Core\Model;

/**
 * 訂位聯絡人。個人訂位時，聯絡人的姓名與國家同時作為賓客資料。
 */
class BookingContact extends Model
{
    /**
     * 新增聯絡人，回傳新增後的 id
     */
    public function create(array $data): int
    {
        $this->db->run(
            'INSERT INTO booking_contact (name, organization, email, phone, country)
             VALUES (:name, :organization, :email, :phone, :country)',
            [
                ':name'         => $data['name'],
                ':organization' => $data['organization'] !== '' ? $data['organization'] : null,
                ':email'        => $data['email'],
                ':phone'        => $data['phone'] !== '' ? $data['phone'] : null,
                ':country'      => $data['country'],
            ]
        );

        return $this->db->lastInsertId();
    }

    /**
     * 依 id 取得聯絡人
     */
    public function find(int $id): ?array
    {
        return $this->db->fetchOne('SELECT * FROM booking_contact WHERE id = ?', [$id]);
    }

    /**
     * 標記已產生通知信
     */
    public function markNotified(int $id): void
    {
        $this->db->run('UPDATE booking_contact SET notified_at = NOW() WHERE id = ?', [$id]);
    }

    /**
     * 驗證聯絡人輸入資料
     *
     * @return array<string, string> 欄位 => 錯誤訊息，空陣列表示通過
     */
    public static function validate(array $data): array
    {
        $errors = [];

        if (($data['name'] ?? '') === '') {
            $errors['name'] = 'Name is required.';
        }

        if (($data['email'] ?? '') === '') {
            $errors['email'] = 'Email is required.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)
            || !preg_match('/^[^@\s]+@[^@\s.]+(\.[^@\s.]+)+$/', $data['email'])) {
            // 需符合 xxx@yyy.zzz 格式
            $errors['email'] = 'Email must match the pattern xxx@yyy.zzz.';
        }

        if (($data['country'] ?? '') === '') {
            $errors['country'] = 'Country is required.';
        } elseif (!\App\Core\Countries::isValid($data['country'])) {
            $errors['country'] = 'Please select a country from the list.';
        }

        if (empty($data['agree'])) {
            $errors['agree'] = 'You must accept the guest regulations before proceeding.';
        }

        return $errors;
    }
}
