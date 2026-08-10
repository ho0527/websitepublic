<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\AdminUser;

/**
 * 後台身分驗證。
 *
 * 登入狀態只存放管理員編號在 Session 中，權限判斷一律回資料庫確認，
 * 避免用戶端可竄改的資料被當成授權依據。
 */
final class Auth
{
    private const SESSION_KEY = 'admin_user_id';

    /**
     * 以帳號密碼登入，成功回傳 true。
     */
    public static function attempt(string $username, string $password): bool
    {
        $admin = AdminUser::findByUsername($username);

        if ($admin === null || !password_verify($password, (string) $admin->password_hash)) {
            return false;
        }

        // 登入後更換 Session ID，避免 Session Fixation
        session_regenerate_id(true);
        Session::put(self::SESSION_KEY, $admin->id());

        return true;
    }

    /**
     * 是否已登入後台。
     */
    public static function check(): bool
    {
        return self::user() !== null;
    }

    /**
     * 取得目前登入的管理員，未登入時回傳 null。
     */
    public static function user(): ?AdminUser
    {
        $id = Session::get(self::SESSION_KEY);

        if ($id === null) {
            return null;
        }

        return AdminUser::find((int) $id);
    }

    /**
     * 登出。
     */
    public static function logout(): void
    {
        Session::forget(self::SESSION_KEY);
        session_regenerate_id(true);
    }
}
