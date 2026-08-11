<?php
/**
 * 前端控制器（front controller）
 * 呼叫目錄本身即可啟動應用程式：http://127.0.0.1:83/worldskill/2015/moduleh/
 *
 * 路由來源支援兩種等效寫法（不需修改 nginx.conf）：
 *   1. PATH_INFO：index.php/booking/individual
 *   2. 查詢字串：index.php?r=booking/individual
 * 若要使用乾淨網址，請參考 README.md 中的 nginx 設定片段。
 */

declare(strict_types=1);

/** @var array $config */
$config = require __DIR__ . '/app/bootstrap.php';

use App\Controllers\BookingController;
use App\Controllers\HomeController;
use App\Core\Request;
use App\Core\Router;
use App\Core\View;

$request = new Request();
$view    = new View(__DIR__ . '/app/Views');
$router  = new Router($request, $view);

$router
    ->add('',                     HomeController::class,    'index')
    ->add('home',                 HomeController::class,    'index')
    ->add('booking/contact',      BookingController::class, 'contact')
    ->add('booking/individual',   BookingController::class, 'individual')
    ->add('booking/group',        BookingController::class, 'group')
    ->add('booking/confirmation', BookingController::class, 'confirmation');

echo $router->dispatch();
