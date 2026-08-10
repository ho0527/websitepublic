<?php
/**
 * PHP 內建網頁伺服器用的路由腳本
 *
 * 啟動方式（在模組資料夾內執行）：
 *     php -S 127.0.0.1:8942 -t . router.php
 *
 * 搭配 nginx 既有的設定：
 *     location /worldskill2024moduleb/ { proxy_pass http://127.0.0.1:8942/; }
 * 就能用試題要求的乾淨網址，例如
 *     http://127.0.0.1:83/worldskill2024moduleb/products/03000123456789.json
 *
 * 這個腳本只做兩件事：
 *   1. 實體存在的靜態檔（css、圖片…）交回內建伺服器處理
 *   2. 其餘請求一律交給 index.php 這個單一入口
 */

declare(strict_types=1);

$requestPath = (string) parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
$localPath   = __DIR__ . rawurldecode($requestPath);

// 靜態檔案直接由內建伺服器輸出（回傳 false 代表交還給 PHP built-in server）
if ($requestPath !== '/' && is_file($localPath)) {
    return false;
}

require __DIR__ . '/index.php';
