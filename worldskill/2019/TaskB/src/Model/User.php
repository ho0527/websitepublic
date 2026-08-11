<?php
/**
 * 使用者資料模型
 */

declare(strict_types=1);

namespace App\Model;

final class User extends BaseModel
{
    public function all(): array
    {
        return $this->db->all(
            'SELECT id, username, display_name, email, role, created_at
             FROM users ORDER BY role ASC, username ASC'
        );
    }

    public function find(int $id): ?array
    {
        return $this->db->one('SELECT * FROM users WHERE id = :id', ['id' => $id]);
    }

    public function findByUsername(string $username): ?array
    {
        return $this->db->one('SELECT * FROM users WHERE username = :u', ['u' => $username]);
    }

    public function create(array $data): int
    {
        $this->db->run(
            'INSERT INTO users (username, password_hash, display_name, email, role)
             VALUES (:username, :hash, :display_name, :email, :role)',
            [
                'username'     => $data['username'],
                'hash'         => password_hash($data['password'], PASSWORD_DEFAULT),
                'display_name' => $data['display_name'],
                'email'        => $data['email'],
                'role'         => $data['role'],
            ]
        );

        return $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $this->db->run(
            'UPDATE users SET display_name = :display_name, email = :email, role = :role WHERE id = :id',
            [
                'display_name' => $data['display_name'],
                'email'        => $data['email'],
                'role'         => $data['role'],
                'id'           => $id,
            ]
        );

        // 密碼留空代表不修改
        if (!empty($data['password'])) {
            $this->db->run(
                'UPDATE users SET password_hash = :hash WHERE id = :id',
                ['hash' => password_hash($data['password'], PASSWORD_DEFAULT), 'id' => $id]
            );
        }
    }

    public function delete(int $id): void
    {
        $this->db->run('DELETE FROM users WHERE id = :id', ['id' => $id]);
    }

    public function countByRole(string $role): int
    {
        return (int) $this->db->value('SELECT COUNT(*) FROM users WHERE role = :r', ['r' => $role]);
    }
}
