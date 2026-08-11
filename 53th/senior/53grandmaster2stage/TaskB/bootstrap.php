<?php
/**
 * 模組 B 測試啟動檔
 *
 * 同時載入：
 *   1. 本目錄的精簡 vendor（PHPUnit 10.1.2）
 *   2. 模組 A 的應用程式類別（TaskA/bootstrap.php）
 *
 * 如此測試可以直接呼叫 App::handle()，不需要啟動網頁伺服器，
 * 而且測得的是真正的 GraphQL 解析、認證與資料庫邏輯。
 */

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';
require dirname(__DIR__) . '/TaskA/bootstrap.php';
require __DIR__ . '/tests/GraphQLTestCase.php';
