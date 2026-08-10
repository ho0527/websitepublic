<?php

declare(strict_types=1);

/**
 * 路由定義。
 *
 * 車次查詢與列車資訊採用「路徑帶參數」的形式而非查詢字串，
 * 以符合試題對搜尋引擎最佳化（SEO）的要求。
 */

use App\Controllers\Admin\AdminBookingController;
use App\Controllers\Admin\AdminTrainController;
use App\Controllers\Admin\AdminTrainTypeController;
use App\Controllers\Admin\AuthController;
use App\Controllers\BookingController;
use App\Controllers\CaptchaController;
use App\Controllers\HomeController;
use App\Controllers\OpenDataController;
use App\Controllers\TrainInfoController;
use App\Core\Router;

$router = new Router();

// --- 前台：首頁與車次查詢 -------------------------------------------------
$router->get('/', [HomeController::class, 'index']);
// 車次查詢的 SEO 網址：/train-lookup/{日期}/{起程站}/{到達站}/{車種}
$router->get('/train-lookup/{date}/{from}/{to}/{trainType}', [HomeController::class, 'lookup']);
// 首頁表單以 GET 送出後，轉為上面的 SEO 網址
$router->get('/train-lookup', [HomeController::class, 'redirectToSeoUrl']);

// --- 前台：列車資訊 -------------------------------------------------------
$router->get('/train-info', [TrainInfoController::class, 'index']);
// 列車資訊的 SEO 網址：/train-info/{車次代碼}
$router->get('/train-info/{code}', [TrainInfoController::class, 'show']);

// --- 前台：訂票 -----------------------------------------------------------
$router->get('/booking', [BookingController::class, 'create']);
$router->post('/booking', [BookingController::class, 'store']);
$router->get('/booking/success/{code}', [BookingController::class, 'success']);
// 供訂票頁動態載入某車次的行經車站
$router->get('/booking/stops/{code}', [BookingController::class, 'stops']);

// --- 前台：訂票查詢 -------------------------------------------------------
$router->get('/bookings', [BookingController::class, 'search']);
$router->post('/bookings/{code}/cancel', [BookingController::class, 'cancel']);

// --- 前台：問答驗證碼（同頁以 AJAX 操作，不另開頁面）---------------------
$router->get('/captcha', [CaptchaController::class, 'show']);
$router->post('/captcha/refresh', [CaptchaController::class, 'refresh']);
$router->post('/captcha/verify', [CaptchaController::class, 'verify']);

// --- 前台：資料統計與開放資料 ---------------------------------------------
$router->get('/statistics', [OpenDataController::class, 'chart']);
$router->get('/statistics/export.json', [OpenDataController::class, 'export']);

// --- 後台：登入 -----------------------------------------------------------
$router->get('/login', [AuthController::class, 'showLoginForm']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/logout', [AuthController::class, 'logout']);

// --- 後台：功能選單 -------------------------------------------------------
$router->get('/admin', [AuthController::class, 'dashboard']);

// --- 後台：車種管理 -------------------------------------------------------
$router->get('/admin/train-types', [AdminTrainTypeController::class, 'index']);
$router->get('/admin/train-types/create', [AdminTrainTypeController::class, 'create']);
$router->post('/admin/train-types', [AdminTrainTypeController::class, 'store']);
$router->get('/admin/train-types/{id}/edit', [AdminTrainTypeController::class, 'edit']);
$router->post('/admin/train-types/{id}', [AdminTrainTypeController::class, 'update']);
$router->post('/admin/train-types/{id}/delete', [AdminTrainTypeController::class, 'destroy']);

// --- 後台：列車管理 -------------------------------------------------------
$router->get('/admin/trains', [AdminTrainController::class, 'index']);
$router->get('/admin/trains/create', [AdminTrainController::class, 'create']);
$router->post('/admin/trains', [AdminTrainController::class, 'store']);
$router->get('/admin/trains/{id}/edit', [AdminTrainController::class, 'edit']);
$router->post('/admin/trains/{id}', [AdminTrainController::class, 'update']);
// 刪除前先確認是否有未發車的訂票
$router->get('/admin/trains/{id}/delete', [AdminTrainController::class, 'confirmDelete']);
$router->post('/admin/trains/{id}/delete', [AdminTrainController::class, 'destroy']);

// --- 後台：訂票紀錄查詢 ---------------------------------------------------
$router->get('/admin/bookings', [AdminBookingController::class, 'index']);
$router->post('/admin/bookings/{code}/cancel', [AdminBookingController::class, 'cancel']);

return $router;
