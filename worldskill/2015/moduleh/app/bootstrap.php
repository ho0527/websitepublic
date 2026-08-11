<?php
/**
 * 應用程式啟動檔：註冊自動載入、建立資料庫連線、啟動 session。
 * 所有進入點（index.php、management/ReservationManagement.php）都先載入本檔。
 */

declare(strict_types=1);

mb_internal_encoding('UTF-8');
date_default_timezone_set('Asia/Taipei');

// ---------------------------------------------------------------------
// PSR-4 風格自動載入：App\Xxx\Yyy => app/Xxx/Yyy.php
// ---------------------------------------------------------------------
spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';

    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $file     = __DIR__ . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $relative) . '.php';

    if (is_file($file)) {
        require_once $file;
    }
});

/** @var array 應用程式設定 */
$config = require __DIR__ . '/config.php';

// 建立資料庫單例
App\Core\Database::getInstance($config['db']);

// 設定網址產生器（cleanUrls 預設關閉，改用 index.php/PATH_INFO 等效形式）
App\Core\Url::configure(
    $config['app']['base_path'],
    (bool) ($config['app']['clean_urls'] ?? false)
);

// 啟動 session（訂位流程需要暫存聯絡人資料）
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name('WSC2015_MODULEH');
    session_start();
}

return $config;
