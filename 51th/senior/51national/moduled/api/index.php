<?php
/**
 * 第51屆全國技能競賽 網頁技術 模組D - 房屋交易平台
 * API 單一入口（Front Controller）
 *
 * 網址形式：
 *   http://127.0.0.1:83/51th/senior/51national/moduled/api/index.php/user/login
 *   若伺服器設定了 rewrite，亦可使用 .../moduled/api/user/login
 */

declare(strict_types=1);

// 錯誤不直接輸出到回應中，避免破壞 JSON 格式
ini_set('display_errors', '0');
error_reporting(E_ALL);

require_once __DIR__ . '/lib/ApiException.php';
require_once __DIR__ . '/lib/Response.php';
require_once __DIR__ . '/lib/Database.php';
require_once __DIR__ . '/lib/MultipartParser.php';
require_once __DIR__ . '/lib/Request.php';
require_once __DIR__ . '/lib/Validator.php';
require_once __DIR__ . '/lib/Auth.php';
require_once __DIR__ . '/lib/ImageService.php';
require_once __DIR__ . '/lib/HouseRepository.php';
require_once __DIR__ . '/lib/UserController.php';
require_once __DIR__ . '/lib/HouseController.php';
require_once __DIR__ . '/lib/ApplicationController.php';
require_once __DIR__ . '/lib/AdController.php';

$config  = require __DIR__ . '/config.php';
$request = new Request();

// 預檢請求直接回應成功
if ($request->method() === 'OPTIONS') {
    Response::success('');
}

/**
 * 取得模組根目錄對應的網址前綴，用來組出圖片的完整網址
 */
function moduleBaseUrl(): string
{
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['SERVER_PORT'] ?? '') === '443');
    $scheme = $isHttps ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';

    // SCRIPT_NAME 例如 /51th/senior/51national/moduled/api/index.php
    $scriptDirectory = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    $moduleDirectory = rtrim(str_replace('\\', '/', dirname($scriptDirectory)), '/');

    return $scheme . '://' . $host . $moduleDirectory;
}

try {
    $pdo     = Database::connection($config['db']);
    $auth    = new Auth($pdo);
    $houses  = new HouseRepository($pdo, $config, moduleBaseUrl());
    $images  = new ImageService($config, dirname(__DIR__));

    $userController        = new UserController($pdo, $auth);
    $houseController       = new HouseController($pdo, $auth, $houses, $images);
    $applicationController = new ApplicationController($pdo, $auth, $houses, $config);
    $adController          = new AdController($pdo, $auth, $houses);

    $method   = $request->method();
    $path     = $request->path();
    $segments = array_values(array_filter(explode('/', $path), static fn($segment) => $segment !== ''));

    // 路由分派
    $route = $method . ' ' . implode('/', $segments);

    switch (true) {
        // API 1：會員登入
        case $route === 'POST user/login':
            $userController->login($request);
            break;

        // API 2：會員登出
        case $route === 'POST user/logout':
            $userController->logout($request);
            break;

        // API 3：會員註冊
        case $route === 'POST user/register':
            $userController->register($request);
            break;

        // API 6：瀏覽自己刊登的房屋列表
        case $route === 'GET user/house':
        case $route === 'GET user/houses':
            $houseController->mine($request);
            break;

        // API 4：取得房屋列表
        case $route === 'GET house':
        case $route === 'GET houses':
            $houseController->index($request);
            break;

        // API 7：刊登房屋
        case $route === 'POST house':
            $houseController->store($request);
            break;

        // API 5、8、9：查看 / 編輯 / 刪除房屋
        case (bool) preg_match('#^(GET|PUT|DELETE) house/(\d+)$#', $route, $matches):
            $houseId = (int) $matches[2];
            if ($matches[1] === 'GET') {
                $houseController->show($request, $houseId);
            } elseif ($matches[1] === 'PUT') {
                $houseController->update($request, $houseId);
            } else {
                $houseController->destroy($request, $houseId);
            }
            break;

        // API 12：取得申請列表
        case $route === 'GET application':
        case $route === 'GET applications':
            $applicationController->index($request);
            break;

        // API 10：申請精選房屋
        case $route === 'POST application':
            $applicationController->store($request);
            break;

        // API 11、13：取消 / 審核申請
        case (bool) preg_match('#^(PUT|DELETE) application/(\d+)$#', $route, $matches):
            $applicationId = (int) $matches[2];
            if ($matches[1] === 'PUT') {
                $applicationController->review($request, $applicationId);
            } else {
                $applicationController->destroy($request, $applicationId);
            }
            break;

        // API 14：取得精選房屋列表
        case $route === 'GET ads':
            $adController->index($request);
            break;

        // API 15：取消精選房屋
        case (bool) preg_match('#^DELETE ads/(\d+)$#', $route, $matches):
            $adController->destroy($request, (int) $matches[1]);
            break;

        default:
            Response::error('MSG_NOT_FOUND', 404);
    }
} catch (ApiException $exception) {
    // 題目規範錯誤時 data 一律為空字串，欄位明細僅在標頭中提供，避免破壞測試比對
    $payload = $exception->getPayload();
    if (is_array($payload) && isset($payload['fields']) && !headers_sent()) {
        header('X-Error-Fields: ' . implode(',', $payload['fields']));
    }
    Response::error($exception->getMessage(), $exception->getStatusCode(), '');
} catch (Throwable $exception) {
    // 未預期的錯誤仍以 JSON 格式回應，方便前端與評分系統判讀
    if (!headers_sent()) {
        header('X-Error-Detail: ' . str_replace(["\r", "\n"], ' ', $exception->getMessage()));
    }
    Response::error('MSG_SERVER_ERROR', 500, '');
}
