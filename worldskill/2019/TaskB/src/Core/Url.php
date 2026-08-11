<?php
/**
 * 網址產生器
 *
 * 依 config 的 clean_urls 決定要輸出
 *   關閉：/worldskill/2019/TaskB/index.php/news/site-updates/
 *   開啟：/worldskill/2019/TaskB/news/site-updates/
 * 兩種形式在程式碼中都以 Url::to('news/site-updates') 呼叫，切換設定即可。
 */

declare(strict_types=1);

namespace App\Core;

final class Url
{
    private static string $basePath = '/';
    private static bool $cleanUrls = false;

    public static function configure(string $basePath, bool $cleanUrls): void
    {
        self::$basePath  = rtrim($basePath, '/') . '/';
        self::$cleanUrls = $cleanUrls;
    }

    /** 網站根目錄（可直接接靜態檔案路徑） */
    public static function base(string $path = ''): string
    {
        return self::$basePath . ltrim($path, '/');
    }

    /** 靜態資源網址（css / js / 圖片） */
    public static function asset(string $path): string
    {
        return self::base($path);
    }

    /** 頁面網址，$path 不含前後斜線，例如 'news/site-updates' */
    public static function to(string $path = ''): string
    {
        $path = trim($path, '/');
        $prefix = self::$cleanUrls ? self::$basePath : self::$basePath . 'index.php/';

        if ($path === '') {
            return self::$cleanUrls ? self::$basePath : self::$basePath . 'index.php/';
        }

        // 檔案型網址（sitemap.xml、robots.txt）不加結尾斜線
        $suffix = str_contains(basename($path), '.') ? '' : '/';

        return $prefix . $path . $suffix;
    }

    /** 目前請求的完整網址（供 canonical / og:url 使用） */
    public static function current(string $path = ''): string
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';

        return $scheme . '://' . $host . self::to($path);
    }
}
