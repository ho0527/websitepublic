<?php

declare(strict_types=1);

/**
 * 第 46 屆全國技能競賽 - 17 網頁設計
 * 模組 D - 列車訂票系統
 *
 * 單一入口（Front Controller）：所有請求都經過這裡，
 * 由路由器決定要交給哪一個控制器處理。
 */

use App\Core\Autoloader;
use App\Core\Config;
use App\Core\Request;
use App\Core\Session;

require __DIR__ . '/app/Core/Autoloader.php';

(new Autoloader('App', __DIR__ . '/app'))->register();

Config::load(__DIR__ . '/config/config.php');
date_default_timezone_set((string) Config::get('timezone', 'Asia/Taipei'));
Session::start();

$router = require __DIR__ . '/routes/web.php';

try {
    $router->dispatch(Request::capture());
} catch (Throwable $exception) {
    // 對外只顯示概略訊息，詳細內容留在伺服器記錄中，避免洩漏系統細節
    http_response_code(500);
    error_log((string) $exception);

    echo '<!DOCTYPE html><html lang="zh-Hant"><head><meta charset="UTF-8">'
        . '<title>系統發生錯誤</title></head><body>'
        . '<h1>系統發生錯誤</h1><p>請稍後再試，或聯絡系統管理員。</p>'
        . '</body></html>';
}
