<?php
/**
 * 共用工具函式
 *
 * 包含 HTML 跳脫、網址組合、JSON 輸出、HTTP 狀態碼回應等。
 */

declare(strict_types=1);

/**
 * HTML 輸出跳脫（防 XSS）。
 *
 * @param mixed $value 任何純量值，null 會被視為空字串
 */
function h($value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * 依目前的佈署模式組出可用的網址。
 *
 * 乾淨網址模式  → /worldskill2024moduleb/products/03000123456789
 * 查詢字串模式  → /worldskill/2024/moduleb/index.php?route=/products/03000123456789
 *
 * @param string               $routePath  以斜線開頭的路由路徑，例如 "/products"
 * @param array<string,mixed>  $queryParameters 額外的查詢字串參數
 */
function urlFor(string $routePath = '/', array $queryParameters = []): string
{
    if ($routePath === '' || $routePath[0] !== '/') {
        $routePath = '/' . $routePath;
    }

    if (USE_CLEAN_URL) {
        $url = PUBLIC_BASE_PATH . ($routePath === '/' ? '/' : $routePath);

        if ($queryParameters !== []) {
            $url .= '?' . http_build_query($queryParameters);
        }

        return $url;
    }

    // 查詢字串模式：所有路由都導向 index.php
    $queryParameters = array_merge(['route' => $routePath], $queryParameters);

    return PUBLIC_BASE_PATH . '/index.php?' . http_build_query($queryParameters);
}

/**
 * 取得靜態資源（css、圖片…）的網址。
 *
 * @param string $relativePath 相對於模組資料夾的路徑，例如 "index.css"
 */
function assetUrl(string $relativePath): string
{
    return PUBLIC_BASE_PATH . '/' . ltrim($relativePath, '/');
}

/**
 * 送出 302 轉址並結束程式。
 */
function redirectTo(string $routePath, array $queryParameters = []): void
{
    header('Location: ' . urlFor($routePath, $queryParameters));
    exit;
}

/**
 * 以 JSON 格式輸出資料並結束程式。
 *
 * @param mixed $payload    要輸出的資料
 * @param int   $statusCode HTTP 狀態碼
 */
function respondWithJson($payload, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    exit;
}

/**
 * 輸出一個簡單的錯誤頁面（HTML）並結束程式。
 */
function respondWithErrorPage(int $statusCode, string $title, string $message): void
{
    http_response_code($statusCode);
    header('Content-Type: text/html; charset=utf-8');

    $safeTitle   = h($title);
    $safeMessage = h($message);
    $loginUrl    = h(urlFor('/login'));
    $styleUrl    = h(assetUrl('index.css'));

    echo <<<HTML
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>{$statusCode} {$safeTitle}</title>
		<link rel="stylesheet" href="{$styleUrl}">
	</head>
	<body>
		<div class="navigationbar">
			<div>{$statusCode} {$safeTitle}</div>
			<div><a href="{$loginUrl}" class="a">login</a></div>
		</div>
		<div class="companymain">
			<div class="companydiv fill cursor-initial">{$safeMessage}</div>
		</div>
	</body>
</html>
HTML;
    exit;
}

/**
 * 取得目前請求的完整網址前綴（協定 + 主機），用於組出 API 分頁的絕對網址。
 */
function currentSchemeAndHost(): string
{
    // 經由 nginx 反向代理時，Host 標頭不含對外埠號，改用設定檔中指定的來源
    if (PHP_SAPI === 'cli-server' && isset($_SERVER['HTTP_X_FORWARDED_FOR']) && REVERSE_PROXY_ORIGIN !== '') {
        return REVERSE_PROXY_ORIGIN;
    }

    $scheme = 'http';

    if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO'])) {
        $scheme = (string) $_SERVER['HTTP_X_FORWARDED_PROTO'];
    } elseif (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        $scheme = 'https';
    }

    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

    return $scheme . '://' . $host;
}

/**
 * 驗證 GTIN 格式：必須是 13 或 14 位純數字。
 */
function isValidGtinFormat(string $gtin): bool
{
    return preg_match('/^\d{13,14}$/', $gtin) === 1;
}
