<?php
/**
 * 模組 C - 資料庫連線與共用函式
 * 說明：集中管理 PDO 連線、JSON 回應與參數讀取，讓 API 與後台共用同一份設定。
 */

declare(strict_types=1);

const DB_HOST = '127.0.0.1';
const DB_PORT = 3306;
const DB_NAME = '56national_modulec';
const DB_USER = 'root';
const DB_PASS = '';

/**
 * 取得（並快取）PDO 連線。
 */
function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', DB_HOST, DB_PORT, DB_NAME);
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);

    return $pdo;
}

/**
 * 取得不指定資料庫的連線，供安裝程式建立資料庫時使用。
 */
function dbServer(): PDO
{
    $dsn = sprintf('mysql:host=%s;port=%d;charset=utf8mb4', DB_HOST, DB_PORT);

    return new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
}

/**
 * 以 JSON 格式輸出並結束程式。
 */
function jsonResponse(mixed $payload, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/**
 * 統一的錯誤輸出。
 */
function jsonError(string $message, int $status = 400): never
{
    jsonResponse(['error' => $message], $status);
}

/**
 * 讀取 query string 中的整數，並限制在合理範圍內。
 */
function queryInt(string $key, int $default, int $min, int $max): int
{
    $raw = $_GET[$key] ?? null;

    if ($raw === null || $raw === '' || !is_numeric($raw)) {
        return $default;
    }

    return max($min, min($max, (int) $raw));
}

/**
 * 讀取 query string 中的日期（YYYY-MM-DD），格式不符時回傳 null。
 */
function queryDate(string $key): ?string
{
    $raw = trim((string) ($_GET[$key] ?? ''));

    if ($raw === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $raw)) {
        return null;
    }

    return $raw;
}
