<?php
/**
 * 全站共用設定與工具函式
 * WorldSkills 2019 TP17 - PHP and JS 模組（Task C：主辦者後台 + 參加者 REST API）
 */

declare(strict_types=1);

// ---------------------------------------------------------------------------
// 資料庫連線設定
// ---------------------------------------------------------------------------
const DB_HOST = '127.0.0.1';
const DB_PORT = 3306;
const DB_NAME = 'worldskill2019_taskc';
const DB_USER = 'root';
const DB_PASS = '';

/**
 * 「今天」的基準日期。
 *
 * 題目與提供的資料集是 2019 年的競賽情境（活動日期為 2019~2020 年），
 * 若直接使用系統當下日期，所有活動都會變成過去式而看不到任何「即將舉行」的活動。
 * 因此預設固定為 2019-09-01 以重現評分當時的資料狀態；
 * 若要改用系統真實日期，把這個常數設成 null 即可。
 */
const REFERENCE_DATE = '2019-09-01';

// ---------------------------------------------------------------------------
// 時區與 Session
// ---------------------------------------------------------------------------
date_default_timezone_set('Asia/Taipei');

/**
 * 取得共用的 PDO 連線（單例）
 */
function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME);
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
    return $pdo;
}

/**
 * 以 prepared statement 執行查詢並取回全部資料列，避免 SQL injection
 *
 * @param array<int|string, mixed> $params
 * @return array<int, array<string, mixed>>
 */
function db_all(string $sql, array $params = []): array
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * 取回單一資料列，找不到時回傳 null
 *
 * @param array<int|string, mixed> $params
 * @return array<string, mixed>|null
 */
function db_one(string $sql, array $params = []): ?array
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    $row = $stmt->fetch();
    return $row === false ? null : $row;
}

/**
 * 執行 INSERT / UPDATE / DELETE，回傳受影響的資料列數
 *
 * @param array<int|string, mixed> $params
 */
function db_exec(string $sql, array $params = []): int
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->rowCount();
}

/**
 * 輸出跳脫，避免 XSS
 */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * 取得基準日期（Y-m-d）
 */
function today(): string
{
    return REFERENCE_DATE ?? date('Y-m-d');
}

/**
 * 取得基準日期時間（Y-m-d H:i:s）
 */
function now(): string
{
    return REFERENCE_DATE === null ? date('Y-m-d H:i:s') : REFERENCE_DATE . ' ' . date('H:i:s');
}

/**
 * 站台根路徑（例如 /worldskill/2019/TaskC/），供產生絕對網址使用
 */
function base_path(): string
{
    // 本檔位於 <root>/inc/config.php，往上一層即為站台根目錄
    $docRoot = str_replace('\\', '/', rtrim((string) ($_SERVER['DOCUMENT_ROOT'] ?? ''), '/\\'));
    $appRoot = str_replace('\\', '/', dirname(__DIR__));
    if ($docRoot !== '' && str_starts_with($appRoot, $docRoot)) {
        return rtrim(substr($appRoot, strlen($docRoot)), '/') . '/';
    }
    return '/worldskill/2019/TaskC/';
}

/**
 * 以站台根路徑為基準組出網址
 */
function url(string $path = ''): string
{
    return base_path() . ltrim($path, '/');
}

/**
 * 轉址並結束程式
 */
function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}
