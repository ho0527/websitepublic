<?php
/**
 * 模組 C - 路由器
 *
 * 支援三種取得路由的方式，讓本模組不論伺服器有沒有設定網址重寫都能運作：
 *  1. PATH_INFO：/modulec/index.php/heritages/slug
 *  2. route 查詢參數：/modulec/index.php?route=heritages/slug
 *     （nginx 的 try_files $uri $uri/ /modulec/index.php?route=$uri 也會落到這一種）
 *  3. REQUEST_URI 直接扣掉基底路徑：/modulec/heritages/slug（伺服器已設定重寫時）
 *
 * 解析後只會產生四種頁面：index（列表）、page（單篇）、tag（標籤）、search（搜尋）。
 */

declare(strict_types=1);

final class Router
{
    /**
     * 取得目前請求的路由字串（去掉前後斜線）
     */
    public static function currentRoute(): string
    {
        // 1) PATH_INFO
        $pathInfo = $_SERVER['PATH_INFO'] ?? '';
        if (is_string($pathInfo) && trim($pathInfo, '/') !== '') {
            return self::normalise($pathInfo);
        }

        // 2) route 查詢參數
        $routeParameter = $_GET['route'] ?? '';
        if (is_string($routeParameter) && trim($routeParameter, '/') !== '') {
            return self::normalise($routeParameter);
        }

        // 3) REQUEST_URI 扣掉模組基底路徑
        $requestUri = (string) ($_SERVER['REQUEST_URI'] ?? '');
        $requestPath = (string) parse_url($requestUri, PHP_URL_PATH);
        $basePath = mc_base_url();

        if ($requestPath !== '' && str_starts_with($requestPath, $basePath)) {
            $remainder = substr($requestPath, strlen($basePath));
            $remainder = preg_replace('#^index\.php#', '', $remainder) ?? $remainder;

            return self::normalise(rawurldecode($remainder));
        }

        return '';
    }

    /**
     * 正規化路由：統一斜線、去掉多餘的斜線與空白片段
     */
    private static function normalise(string $route): string
    {
        $route = str_replace('\\', '/', $route);
        $segments = array_filter(explode('/', $route), static fn (string $s): bool => $s !== '');

        return implode('/', $segments);
    }

    /**
     * 把路由解析成控制器要用的資訊
     *
     * @return array{type: string, path: string}
     *         type：index | page-or-folder | tag | search | not-found
     */
    public static function resolve(string $route): array
    {
        if ($route === '' || $route === 'index.php') {
            return ['type' => 'index', 'path' => ''];
        }

        // /heritages 與 /heritages/... → 內容資料夾或單篇文章
        if ($route === 'heritages') {
            return ['type' => 'index', 'path' => ''];
        }
        if (str_starts_with($route, 'heritages/')) {
            return ['type' => 'page-or-folder', 'path' => substr($route, strlen('heritages/'))];
        }

        // /tags/tag-name-here → 標籤查詢
        if ($route === 'tags') {
            return ['type' => 'tag', 'path' => ''];
        }
        if (str_starts_with($route, 'tags/')) {
            return ['type' => 'tag', 'path' => substr($route, strlen('tags/'))];
        }

        // /search → 搜尋結果（關鍵字由 q 查詢參數提供）
        if ($route === 'search') {
            return ['type' => 'search', 'path' => ''];
        }

        return ['type' => 'not-found', 'path' => $route];
    }
}
