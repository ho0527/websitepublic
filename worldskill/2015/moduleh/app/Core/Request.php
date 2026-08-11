<?php
namespace App\Core;

/**
 * 封裝 HTTP 請求：路由字串、輸入參數與 session 便利方法。
 */
class Request
{
    /** @var string 目前的路由（例如 booking/individual） */
    private string $route;

    public function __construct()
    {
        $this->route = $this->resolveRoute();
    }

    /**
     * 解析路由來源，依序支援：
     *  1. PATH_INFO（index.php/booking/individual 或乾淨網址 rewrite）
     *  2. 查詢字串 ?r=booking/individual（無 rewrite 時的等效寫法）
     */
    private function resolveRoute(): string
    {
        $path = $_SERVER['PATH_INFO'] ?? '';

        if ($path === '' && isset($_GET['r'])) {
            $path = (string) $_GET['r'];
        }

        // 僅允許英數、斜線、底線與連字號，避免路徑穿越
        $path = preg_replace('#[^a-zA-Z0-9_/\-]#', '', $path) ?? '';

        return trim($path, '/');
    }

    public function route(): string
    {
        return $this->route;
    }

    public function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public function isPost(): bool
    {
        return $this->method() === 'POST';
    }

    /**
     * 取得 POST 值（字串，已 trim）
     */
    public function post(string $key, string $default = ''): string
    {
        $value = $_POST[$key] ?? $default;

        return is_string($value) ? trim($value) : $default;
    }

    /**
     * 取得 POST 陣列值
     */
    public function postArray(string $key): array
    {
        $value = $_POST[$key] ?? [];

        return is_array($value) ? $value : [];
    }

    /**
     * 判斷某個 POST 欄位（通常是 submit 按鈕）是否存在
     */
    public function hasPost(string $key): bool
    {
        return array_key_exists($key, $_POST);
    }

    /**
     * 取得 GET 值
     */
    public function query(string $key, string $default = ''): string
    {
        $value = $_GET[$key] ?? $default;

        return is_string($value) ? trim($value) : $default;
    }

    /**
     * 讀取 session
     */
    public function session(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * 寫入 session
     */
    public function setSession(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * 移除 session
     */
    public function forgetSession(string $key): void
    {
        unset($_SESSION[$key]);
    }
}
