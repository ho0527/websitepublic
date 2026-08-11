<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\DiningModule;

/**
 * 首頁：動態顯示四種用餐體驗說明（資料來自 dining_module / seating）。
 */
class HomeController extends Controller
{
    public function index(): string
    {
        $modules = (new DiningModule())->allWithSeatings();

        return $this->render('home', [
            'pageTitle'  => 'Guests in Restaurant Service',
            'breadcrumb' => ['Information'],
            'modules'    => $modules,
        ]);
    }
}
