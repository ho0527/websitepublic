<?php
/**
 * 公開產品頁面的靜態網址進入點
 *
 * 乾淨網址模式下可直接使用 /01/[GTIN]；
 * 在沒有 rewrite 規則的 nginx 靜態模式下，可用 /01/?gtin=[GTIN] 進入同一個頁面。
 */

declare(strict_types=1);

$requestedGtin = preg_replace('/\D/', '', (string) ($_GET['gtin'] ?? ''));

$_GET['route'] = '/01/' . $requestedGtin;

require __DIR__ . '/../index.php';
