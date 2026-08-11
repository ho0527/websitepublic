<?php
namespace App\Core;

/**
 * 網址產生器。
 *
 * 預設使用 PATH_INFO 形式（index.php/booking/individual），
 * 此形式在未設定 rewrite 的 nginx 上即可運作；
 * 若在 config 中開啟 clean_urls（並套用 README 中的 nginx 設定片段），
 * 則會輸出乾淨網址（booking/individual）。
 */
class Url
{
    /** @var string 模組在網站根目錄下的路徑，例如 /worldskill/2015/moduleh */
    private static string $basePath = '';

    /** @var bool 是否輸出乾淨網址 */
    private static bool $cleanUrls = false;

    public static function configure(string $basePath, bool $cleanUrls = false): void
    {
        self::$basePath  = rtrim($basePath, '/');
        self::$cleanUrls = $cleanUrls;
    }

    /**
     * 產生應用程式路由網址
     */
    public static function to(string $route = ''): string
    {
        $route = trim($route, '/');

        if (self::$cleanUrls) {
            return self::$basePath . ($route === '' ? '/' : '/' . $route);
        }

        return self::$basePath . '/index.php' . ($route === '' ? '' : '/' . $route);
    }

    /**
     * 產生靜態資源網址
     */
    public static function asset(string $path): string
    {
        return self::$basePath . '/assets/' . ltrim($path, '/');
    }

    /**
     * 產生管理區網址（實體檔案 management/ReservationManagement.php）
     */
    public static function management(string $query = ''): string
    {
        return self::managementPage('ReservationManagement.php', $query);
    }

    /**
     * 產生管理區其他頁面的網址
     */
    public static function managementPage(string $file, string $query = ''): string
    {
        return self::$basePath . '/management/' . $file
            . ($query === '' ? '' : '?' . $query);
    }
}
