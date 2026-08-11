<?php
/**
 * A1c - 主辦者登出：清除 session 後回到登入頁
 */

declare(strict_types=1);

require_once __DIR__ . '/inc/auth.php';

start_session();
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}
session_destroy();

// 避免按上一頁還能看到後台內容
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
redirect('index.php');
