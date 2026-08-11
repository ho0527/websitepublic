<?php
/**
 * 餐廳外場主管的賓客名單（Generate Guest List）
 *   畫面：GuestList.php
 *   CSV ：GuestList.php?format=csv
 */

declare(strict_types=1);

/** @var array $config */
$config = require __DIR__ . '/../app/bootstrap.php';

use App\Controllers\ManagementController;
use App\Core\Request;
use App\Core\View;

$request    = new Request();
$controller = new ManagementController($request, new View(__DIR__ . '/../app/Views'));
$controller->setConfig($config);

if ($request->query('format') === 'csv') {
    $controller->guestListCsv();
    exit;
}

echo $controller->guestList();
