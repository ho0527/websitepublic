<?php
/**
 * 模組 A GraphQL API 進入點
 *
 * 端點（三種寫法皆可，內容完全相同）：
 *   POST /53th/senior/53grandmaster2stage/TaskA/
 *   POST /53th/senior/53grandmaster2stage/TaskA/index.php
 *   POST /53th/senior/53grandmaster2stage/TaskA/index.php/graphql
 *
 * 以瀏覽器 GET 開啟時，會顯示一個內建的查詢主控台（不引用任何外部 CDN）。
 */

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

// 允許跨來源呼叫，方便以其他頁面或工具測試
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$rawBody = file_get_contents('php://input') ?: '';
$request = parseRequest($rawBody);

// GET 且沒有帶查詢時顯示主控台頁面
if ($method === 'GET' && trim($request['query']) === '') {
    header('Content-Type: text/html; charset=utf-8');
    require __DIR__ . '/console.php';
    exit;
}

$token    = App::extractBearerToken($_SERVER['HTTP_AUTHORIZATION'] ?? null);
$response = App::handle($request['query'], $request['variables'], $request['operationName'], $token);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);

/**
 * 解析請求內容，支援三種常見格式：
 *   1. application/json      {"query": "...", "variables": {...}, "operationName": "..."}
 *   2. application/graphql   直接送出查詢字串
 *   3. 表單或查詢字串        query=...&variables=...
 *
 * @return array{query:string, variables:array, operationName:?string}
 */
function parseRequest(string $rawBody): array
{
    $contentType = strtolower($_SERVER['CONTENT_TYPE'] ?? '');

    // application/graphql：整個 body 就是查詢
    if (str_contains($contentType, 'application/graphql')) {
        return ['query' => $rawBody, 'variables' => [], 'operationName' => null];
    }

    // JSON
    if ($rawBody !== '' && str_contains($contentType, 'json')) {
        $decoded = json_decode($rawBody, true);
        if (is_array($decoded)) {
            return normalizeRequest($decoded);
        }
    }

    // 未指定 Content-Type 但 body 看起來像 JSON，也一併嘗試
    if ($rawBody !== '' && $contentType === '') {
        $decoded = json_decode($rawBody, true);
        if (is_array($decoded)) {
            return normalizeRequest($decoded);
        }

        return ['query' => $rawBody, 'variables' => [], 'operationName' => null];
    }

    // 表單／查詢字串
    $source = $_POST !== [] ? $_POST : $_GET;

    return normalizeRequest($source);
}

/**
 * 將來源陣列整理成統一結構
 *
 * @return array{query:string, variables:array, operationName:?string}
 */
function normalizeRequest(array $source): array
{
    $variables = $source['variables'] ?? [];
    if (is_string($variables)) {
        $variables = json_decode($variables, true) ?: [];
    }

    return [
        'query'         => (string) ($source['query'] ?? ''),
        'variables'     => is_array($variables) ? $variables : [],
        'operationName' => isset($source['operationName']) && $source['operationName'] !== ''
            ? (string) $source['operationName']
            : null,
    ];
}
