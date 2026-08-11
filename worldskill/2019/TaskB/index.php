<?php
/**
 * 單一入口（front controller）
 *
 * WorldSkills 2019 Skill 17 模組 B — Kazan MuseumTour CMS
 *
 * 網址形式（clean_urls = false 時，不需要修改 nginx 設定）：
 *   前台首頁      index.php/
 *   博物館頁      index.php/<museum-slug>/
 *   全部新聞      index.php/news/
 *   分類新聞      index.php/news/<category-slug>/
 *   單篇新聞      index.php/news/<category-slug>/<post-slug>/
 *   後台          index.php/admin/
 */

declare(strict_types=1);

use App\Core\App;
use App\Core\Router;

require __DIR__ . '/bootstrap.php';

/** @var App $app 由 bootstrap 建立 */
$router = new Router($app);
$router->dispatch(Router::currentPath());
