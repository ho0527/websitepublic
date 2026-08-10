<?php
/**
 * 管理員身分驗證
 *
 * 依試題要求，這是三小時內完成的原型，只使用單一 passphrase 檢查，
 * 登入狀態存放在 PHP session 中。
 */

declare(strict_types=1);

final class Auth
{
    /** session 中用來記錄登入狀態的鍵值 */
    private const SESSION_KEY = 'is_admin_signed_in';

    /**
     * 啟動 session（重複呼叫不會有副作用）。
     */
    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        session_name(SESSION_NAME);
        session_start();
    }

    /**
     * 以 passphrase 嘗試登入。
     *
     * @return bool 密碼正確回傳 true
     */
    public static function attemptSignIn(string $passphrase): bool
    {
        // 使用 hash_equals 做定時比較，避免時間差攻擊
        if (!hash_equals(ADMIN_PASSPHRASE, $passphrase)) {
            return false;
        }

        self::startSession();
        // 登入成功後更換 session id，降低 session fixation 風險
        session_regenerate_id(true);
        $_SESSION[self::SESSION_KEY] = true;

        return true;
    }

    /**
     * 登出並清除 session。
     */
    public static function signOut(): void
    {
        self::startSession();
        $_SESSION = [];
        session_destroy();
    }

    /**
     * 目前是否為已登入的管理員。
     */
    public static function isSignedIn(): bool
    {
        self::startSession();

        return ($_SESSION[self::SESSION_KEY] ?? false) === true;
    }

    /**
     * 保護管理頁面：未登入時直接回應 401。
     *
     * @param bool $wantsJson 若請求的是 JSON API，錯誤訊息也以 JSON 回傳
     */
    public static function requireAdmin(bool $wantsJson = false): void
    {
        if (self::isSignedIn()) {
            return;
        }

        if ($wantsJson) {
            respondWithJson(['error' => 'Unauthorized', 'message' => 'Admin login is required.'], 401);
        }

        respondWithErrorPage(
            401,
            'Unauthorized',
            'Admin login is required before accessing the management functions.'
        );
    }
}
