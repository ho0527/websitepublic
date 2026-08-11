<?php
/**
 * 訂位管理頁（WSI 工作人員專用）
 * 規格要求：必須位於模組目錄下的 /management 子目錄，且檔名為 ReservationManagement.php
 *
 * 網址：http://127.0.0.1:83/worldskill/2015/moduleh/management/ReservationManagement.php
 */

declare(strict_types=1);

/** @var array $config */
$config = require __DIR__ . '/../app/bootstrap.php';

use App\Controllers\ManagementController;
use App\Core\Request;
use App\Core\View;

$controller = new ManagementController(
    new Request(),
    new View(__DIR__ . '/../app/Views')
);
$controller->setConfig($config);

echo $controller->index();
