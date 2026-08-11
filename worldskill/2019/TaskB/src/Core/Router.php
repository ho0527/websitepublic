<?php
/**
 * 路由分派
 *
 * 支援兩種網址形式：
 *   1. PATH_INFO：index.php/news/site-updates/（預設，nginx 無須額外設定）
 *   2. 乾淨網址：/news/site-updates/（需在 nginx 加 try_files，見 README）
 */

declare(strict_types=1);

namespace App\Core;

use App\Controller\AdminController;
use App\Controller\FrontController;

final class Router
{
    public function __construct(private App $app)
    {
    }

    /**
     * 解析目前請求對應的站內路徑，例如 'news/site-updates'
     */
    /**
     * 一律從 REQUEST_URI 推算站內路徑。
     *
     * 不直接採用 PATH_INFO 的原因：當網址剛好只有結尾斜線（index.php/）時，
     * nginx 的 fastcgi_split_path_info 不會成立，PATH_INFO 會拿到未解析的字面值，
     * 從 REQUEST_URI 扣掉專案根路徑則兩種形式都正確。
     */
    public static function currentPath(): string
    {
        $uri  = parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?: '/';
        $uri  = urldecode($uri);
        $base = Url::base();

        if (str_starts_with($uri, $base)) {
            $uri = substr($uri, strlen($base));
        }

        $uri = ltrim($uri, '/');

        // 去掉入口檔名，讓 index.php/news 與 /news 得到相同結果
        if ($uri === 'index.php' || str_starts_with($uri, 'index.php/')) {
            $uri = substr($uri, strlen('index.php'));
        }

        return trim($uri, '/');
    }

    public function dispatch(string $path): void
    {
        $segments = $path === '' ? [] : explode('/', $path);
        $adminKey = trim((string) $this->app->config['admin_path'], '/');

        if (($segments[0] ?? '') === $adminKey) {
            (new AdminController($this->app))->handle(array_slice($segments, 1));

            return;
        }

        (new FrontController($this->app))->handle($segments, $path);
    }

    /** 導向站內路徑 */
    public static function redirect(string $path): never
    {
        header('Location: ' . Url::to($path));
        exit;
    }

    /** 導向完整網址（含查詢字串） */
    public static function redirectTo(string $url): never
    {
        header('Location: ' . $url);
        exit;
    }
}
