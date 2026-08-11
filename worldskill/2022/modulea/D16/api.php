<?php
/**
 * D16 API Request Logger
 *
 * 接收 Content-Type: application/json 的 POST 請求，
 * 並把 request body 原文寫入 logs/ 資料夾內的文字檔。
 *
 * 【檔名說明】
 * 題目要求檔名為 HH:MM:SS-request.txt，但 Windows 檔案系統不允許檔名含冒號（:），
 * 因此本題改用連字號：HH-MM-SS-request.txt（同一秒內重複呼叫會自動附加序號避免覆蓋）。
 * 詳見同資料夾的 README.txt。
 *
 * HTTP 狀態碼：
 * - 405 非 POST 方法
 * - 415 Content-Type 不是 application/json
 * - 400 JSON 格式錯誤或 body 為空
 * - 500 檔案寫入失敗
 * - 200 成功
 */

// PHP 預設時區為 UTC，明確設為本地時區，讓檔名時間與實際牆上時間一致
date_default_timezone_set('Asia/Taipei');

header('Content-Type: application/json; charset=utf-8');

/**
 * 以 JSON 回應並結束程式
 *
 * @param int   $statusCode HTTP 狀態碼
 * @param array $payload    回應內容
 */
function respond(int $statusCode, array $payload): void
{
    http_response_code($statusCode);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

// ---------- 1. 只接受 POST ----------
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    respond(405, [
        'success' => false,
        'message' => 'Method Not Allowed，本端點只接受 POST。',
    ]);
}

// ---------- 2. 檢查 Content-Type ----------
$contentType = $_SERVER['CONTENT_TYPE'] ?? ($_SERVER['HTTP_CONTENT_TYPE'] ?? '');

if (stripos($contentType, 'application/json') === false) {
    respond(415, [
        'success' => false,
        'message' => 'Unsupported Media Type，Content-Type 必須為 application/json。',
        'received_content_type' => $contentType,
    ]);
}

// ---------- 3. 讀取並驗證 JSON body ----------
$rawBody = file_get_contents('php://input');

if ($rawBody === false || trim($rawBody) === '') {
    respond(400, [
        'success' => false,
        'message' => 'Bad Request，request body 不可為空。',
    ]);
}

json_decode($rawBody);

if (json_last_error() !== JSON_ERROR_NONE) {
    respond(400, [
        'success' => false,
        'message' => 'Bad Request，JSON 格式錯誤：' . json_last_error_msg(),
    ]);
}

// ---------- 4. 確保 logs/ 資料夾存在 ----------
$logDirectory = __DIR__ . '/logs';

if (!is_dir($logDirectory) && !mkdir($logDirectory, 0777, true) && !is_dir($logDirectory)) {
    respond(500, [
        'success' => false,
        'message' => '無法建立 logs 資料夾。',
    ]);
}

// ---------- 5. 產生檔名（同秒重複呼叫時附加序號） ----------
$baseName = date('H-i-s') . '-request';
$fileName = $baseName . '.txt';
$sequence = 1;

while (file_exists($logDirectory . '/' . $fileName)) {
    $fileName = $baseName . '-' . $sequence . '.txt';
    $sequence++;
}

// ---------- 6. 寫入檔案 ----------
$written = file_put_contents($logDirectory . '/' . $fileName, $rawBody);

if ($written === false) {
    respond(500, [
        'success' => false,
        'message' => '寫入檔案失敗，請確認 logs 資料夾的寫入權限。',
    ]);
}

respond(200, [
    'success'   => true,
    'message'   => 'Request body 已儲存。',
    'file'      => $fileName,
    'path'      => 'logs/' . $fileName,
    'bytes'     => $written,
    'saved_at'  => date('Y-m-d H:i:s'),
]);
