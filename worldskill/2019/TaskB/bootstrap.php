<?php
/**
 * 啟動檔：自動載入、Session、錯誤處理、建立應用程式容器
 */

declare(strict_types=1);

use App\Core\App;

$config = require __DIR__ . '/config.php';

/**
 * PSR-4 風格的簡易自動載入器：App\Core\Foo → src/Core/Foo.php
 */
spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relative = str_replace('\\', '/', substr($class, strlen($prefix)));
    $file     = __DIR__ . '/src/' . $relative . '.php';

    if (is_file($file)) {
        require $file;
    }
});

// 開發用：把錯誤顯示出來，方便比賽時除錯
error_reporting(E_ALL);
ini_set('display_errors', '1');
date_default_timezone_set('Europe/Moscow'); // 網站以喀山（Kazan）當地時間為準

// Session：僅允許 cookie 傳遞、限制在本專案路徑之下
session_name($config['session_name']);
session_set_cookie_params([
    'path'     => $config['base_path'],
    'httponly' => true,
    'samesite' => 'Lax',
]);
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$app = new App($config, __DIR__);

return $app;
