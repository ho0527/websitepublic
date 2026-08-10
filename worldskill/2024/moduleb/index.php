<?php
/**
 * 模組 B 單一入口（front controller）
 *
 * 所有請求都由這個檔案解析路由後分派給對應的控制器。
 * 路由來源依序為：
 *   1. $_GET['route']（查詢字串模式，或由子資料夾的 index.php 預先指定）
 *   2. REQUEST_URI 去掉網址前綴之後的路徑（乾淨網址模式）
 */

declare(strict_types=1);

require __DIR__ . '/config.php';
require __DIR__ . '/src/Helper.php';
require __DIR__ . '/src/Database.php';
require __DIR__ . '/src/Auth.php';
require __DIR__ . '/src/View.php';
require __DIR__ . '/src/ImageUploader.php';
require __DIR__ . '/src/CompanyRepository.php';
require __DIR__ . '/src/ProductRepository.php';
require __DIR__ . '/src/AdminController.php';
require __DIR__ . '/src/ApiController.php';
require __DIR__ . '/src/PublicController.php';

/**
 * 把路徑正規化成 "/xxx/yyy"（開頭一個斜線、結尾不含斜線）。
 */
function normalizeRoutePath(string $path): string
{
    $path = trim($path);
    $path = '/' . trim($path, '/');

    return $path === '/' ? '/' : $path;
}

/**
 * 解析目前請求要走的路由路徑。
 */
function resolveRoutePath(): string
{
    if (isset($_GET['route']) && $_GET['route'] !== '') {
        return normalizeRoutePath((string) $_GET['route']);
    }

    $requestPath = (string) parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
    $requestPath = rawurldecode($requestPath);

    // 去掉對外網址前綴，例如 /worldskill2024moduleb 或 /worldskill/2024/moduleb
    if (PUBLIC_BASE_PATH !== '' && strncmp($requestPath, PUBLIC_BASE_PATH, strlen(PUBLIC_BASE_PATH)) === 0) {
        $requestPath = substr($requestPath, strlen(PUBLIC_BASE_PATH));
    }

    // 去掉可能出現在路徑中的 index.php
    $requestPath = (string) preg_replace('#^/index\.php#', '', $requestPath);

    return normalizeRoutePath($requestPath);
}

/**
 * 路由表：每一列為 [HTTP 方法, 路徑樣式, 處理函式]。
 * 路徑樣式中的括號會依序成為處理函式的參數。
 *
 * @return array<int,array{0:string,1:string,2:callable}>
 */
function buildRouteTable(): array
{
    $admin  = new AdminController();
    $api    = new ApiController();
    $public = new PublicController();

    return [
        // --- 首頁與登入 ---
        ['GET',  '#^/$#',        fn () => $admin->home()],
        ['GET',  '#^/login$#',   fn () => $admin->showLoginForm()],
        ['POST', '#^/login$#',   fn () => $admin->signIn()],
        ['GET',  '#^/logout$#',  fn () => $admin->signOut()],
        ['POST', '#^/logout$#',  fn () => $admin->signOut()],

        // --- 公司管理 ---
        ['GET',  '#^/companies$#',                fn () => $admin->companyList()],
        ['POST', '#^/companies$#',                fn () => $admin->companyCreate()],
        ['GET',  '#^/companies/new$#',            fn () => $admin->companyCreateForm()],
        ['GET',  '#^/companies/deactivated$#',    fn () => $admin->deactivatedCompanyList()],
        ['GET',  '#^/companies/(\d+)$#',          fn (string $id) => $admin->companyDetail((int) $id)],
        ['POST', '#^/companies/(\d+)$#',          fn (string $id) => $admin->companyUpdate((int) $id)],
        ['GET',  '#^/companies/(\d+)/edit$#',     fn (string $id) => $admin->companyEditForm((int) $id)],
        ['POST', '#^/companies/(\d+)/deactivate$#', fn (string $id) => $admin->companyDeactivate((int) $id)],
        ['POST', '#^/companies/(\d+)/activate$#', fn (string $id) => $admin->companyActivate((int) $id)],

        // --- 產品 JSON API（必須排在產品頁面路由之前，避免被誤判） ---
        ['GET',  '#^/products\.json$#',                 fn () => $api->productList()],
        ['GET',  '#^/products/(\d{13,14})\.json$#',     fn (string $gtin) => $api->productDetail($gtin)],

        // --- 產品管理 ---
        ['GET',  '#^/products$#',                       fn () => $admin->productList()],
        ['POST', '#^/products$#',                       fn () => $admin->productCreate()],
        ['GET',  '#^/products/new$#',                   fn () => $admin->productCreateForm()],
        ['GET',  '#^/products/hidden$#',                fn () => $admin->hiddenProductList()],
        ['GET',  '#^/products/(\d{13,14})$#',           fn (string $gtin) => $admin->productDetail($gtin)],
        ['POST', '#^/products/(\d{13,14})$#',           fn (string $gtin) => $admin->productUpdate($gtin)],
        ['GET',  '#^/products/(\d{13,14})/edit$#',      fn (string $gtin) => $admin->productEditForm($gtin)],
        ['POST', '#^/products/(\d{13,14})/hide$#',      fn (string $gtin) => $admin->productSetHidden($gtin, true)],
        ['POST', '#^/products/(\d{13,14})/unhide$#',    fn (string $gtin) => $admin->productSetHidden($gtin, false)],
        ['POST', '#^/products/(\d{13,14})/delete$#',    fn (string $gtin) => $admin->productDelete($gtin)],
        ['POST', '#^/products/(\d{13,14})/image/remove$#', fn (string $gtin) => $admin->productRemoveImage($gtin)],

        // --- 公開頁面 ---
        // /gtintest 是沿用原本前端的頁面名稱，與 /gtin 指向同一個功能
        ['GET',  '#^/gtin(?:test)?$#',  fn () => $public->gtinVerification()],
        ['POST', '#^/gtin(?:test)?$#',  fn () => $public->gtinVerification()],
        ['GET',  '#^/01/(\d{13,14})$#', fn (string $gtin) => $public->publicProductPage($gtin)],
    ];
}

/**
 * 依請求方法與路徑分派到對應的處理函式。
 */
function dispatchRequest(string $requestMethod, string $routePath): void
{
    $pathMatchedButMethodNot = false;

    foreach (buildRouteTable() as [$allowedMethod, $pattern, $handler]) {
        if (preg_match($pattern, $routePath, $matches) !== 1) {
            continue;
        }

        if ($allowedMethod !== $requestMethod) {
            $pathMatchedButMethodNot = true;
            continue;
        }

        array_shift($matches);
        $handler(...$matches);

        return;
    }

    if ($pathMatchedButMethodNot) {
        respondWithErrorPage(405, 'Method Not Allowed', 'The HTTP method is not supported on this URL.');
    }

    // 找不到路由時，JSON 路徑回 JSON、其餘回 HTML
    if (str_ends_with($routePath, '.json')) {
        respondWithJson(['error' => 'Not Found', 'message' => 'Unknown API endpoint.'], 404);
    }

    respondWithErrorPage(404, 'Not Found', 'The requested page does not exist: ' . $routePath);
}

// ---------------------------------------------------------------------------
// 主流程
// ---------------------------------------------------------------------------
try {
    Auth::startSession();
    dispatchRequest((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'), resolveRoutePath());
} catch (Throwable $exception) {
    // 統一處理未預期的錯誤，避免把資料庫細節直接吐給使用者
    error_log('[worldskill2024_moduleb] ' . $exception->getMessage());
    respondWithErrorPage(500, 'Internal Server Error', $exception->getMessage());
}
