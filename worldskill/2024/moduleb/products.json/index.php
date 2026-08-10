<?php
/**
 * 靜態網址進入點
 *
 * 讓 nginx 不需要 rewrite 規則也能用 /products.json 這個網址進入單一入口。
 * 實際的處理仍由模組根目錄的 index.php 負責。
 */

declare(strict_types=1);

$_GET['route'] = '/products.json';

require __DIR__ . '/../index.php';
