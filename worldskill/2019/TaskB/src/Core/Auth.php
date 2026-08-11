<?php
/**
 * 後台身分驗證與權限控管
 *
 * 角色：
 *   admin  — 完整後台權限（內容、分類、使用者、外掛、設定、安全性紀錄）
 *   editor — 只能管理內容（博物館、文章、分類、媒體）
 */

declare(strict_types=1);

namespace App\Core;

use App\Model\LoginAttempt;
use App\Model\User;

final class Auth
{
    /** 各能力所允許的角色 */
    private const CAPABILITIES = [
        'manage_museums'  => ['admin', 'editor'],
        'manage_posts'    => ['admin', 'editor'],
        'manage_media'    => ['admin', 'editor'],
        'manage_terms'    => ['admin', 'editor'],
        'manage_users'    => ['admin'],
        'manage_plugins'  => ['admin'],
        'manage_settings' => ['admin'],
        'view_security'   => ['admin'],
    ];

    private ?array $user = null;

    public function __construct(
        private User $users,
        private LoginAttempt $attempts,
        private int $maxAttempts,
        private int $lockoutMinutes
    ) {
        if (isset($_SESSION['user_id'])) {
            $this->user = $this->users->find((int) $_SESSION['user_id']);
        }
    }

    public function user(): ?array
    {
        return $this->user;
    }

    public function check(): bool
    {
        return $this->user !== null;
    }

    public function role(): string
    {
        return $this->user['role'] ?? 'guest';
    }

    /** 是否具備某項能力 */
    public function can(string $capability): bool
    {
        $allowed = self::CAPABILITIES[$capability] ?? [];

        return in_array($this->role(), $allowed, true);
    }

    /** 目前來源 IP 是否因連續登入失敗而被鎖定 */
    public function isLockedOut(): bool
    {
        $failures = $this->attempts->recentFailures($this->clientIp(), $this->lockoutMinutes);

        return $failures >= $this->maxAttempts;
    }

    public function lockoutMinutes(): int
    {
        return $this->lockoutMinutes;
    }

    /** 嘗試登入，成功回傳 true */
    public function attempt(string $username, string $password): bool
    {
        $user    = $this->users->findByUsername($username);
        $success = $user !== null && password_verify($password, $user['password_hash']);

        $this->attempts->log($username, $this->clientIp(), $this->userAgent(), $success);

        if (!$success) {
            return false;
        }

        // 登入成功後更換 session id，避免 session fixation
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        $this->user          = $user;

        return true;
    }

    public function logout(): void
    {
        $this->user = null;
        unset($_SESSION['user_id']);
        session_regenerate_id(true);
    }

    public function clientIp(): string
    {
        return (string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
    }

    private function userAgent(): string
    {
        return mb_substr((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255);
    }
}
