<?php
/**
 * 登入嘗試紀錄（Site Guardian 安全性外掛使用）
 */

declare(strict_types=1);

namespace App\Model;

final class LoginAttempt extends BaseModel
{
    public function log(string $username, string $ip, string $userAgent, bool $success): void
    {
        $this->db->run(
            'INSERT INTO login_attempts (username, ip_address, user_agent, is_success)
             VALUES (:username, :ip, :ua, :ok)',
            [
                'username' => mb_substr($username, 0, 60),
                'ip'       => $ip,
                'ua'       => $userAgent,
                'ok'       => $success ? 1 : 0,
            ]
        );
    }

    /** 指定 IP 在最近 N 分鐘內的失敗次數 */
    public function recentFailures(string $ip, int $minutes): int
    {
        // 分鐘數先轉成整數再組進 SQL（避免以字串繫結造成 INTERVAL 型別問題），IP 仍以參數繫結
        $minutes = max(1, $minutes);

        return (int) $this->db->value(
            'SELECT COUNT(*) FROM login_attempts
             WHERE ip_address = :ip AND is_success = 0
               AND created_at >= (NOW() - INTERVAL ' . $minutes . ' MINUTE)',
            ['ip' => $ip]
        );
    }

    public function recent(int $limit = 50): array
    {
        return $this->db->all(
            'SELECT * FROM login_attempts ORDER BY created_at DESC, id DESC LIMIT ' . max(1, $limit)
        );
    }

    public function countFailures(): int
    {
        return (int) $this->db->value('SELECT COUNT(*) FROM login_attempts WHERE is_success = 0');
    }

    /** 清除紀錄（後台按鈕） */
    public function clear(): void
    {
        $this->db->run('DELETE FROM login_attempts');
    }
}
