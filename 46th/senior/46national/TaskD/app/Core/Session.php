<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Session 存取的薄封裝，另外提供「只顯示一次」的快閃訊息。
 */
final class Session
{
    /**
     * 啟動 Session（若尚未啟動）。
     */
    public static function start(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public static function put(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * 清空整個 Session 並重新產生識別碼。
     */
    public static function destroy(): void
    {
        $_SESSION = [];
        session_regenerate_id(true);
    }

    /**
     * 寫入一則快閃訊息，讀取後即消失。
     */
    public static function flash(string $key, mixed $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    /**
     * 讀取並清除快閃訊息。
     */
    public static function pullFlash(string $key, mixed $default = null): mixed
    {
        $value = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);

        return $value;
    }
}
