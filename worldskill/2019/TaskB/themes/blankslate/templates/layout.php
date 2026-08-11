<?php
/**
 * 父主題（BlankSlate）：版面骨架
 *
 * 這個檔案只負責 HTML 文件結構；頁首、選單、頁尾等區塊會優先採用
 * 子主題 Kazan_MuseumTour 之下的同名 partial。
 *
 * @var \App\Core\App   $app
 * @var \App\Core\Theme $theme
 * @var array           $page    頁面中繼資料
 * @var string          $content 已渲染完成的內容區塊
 */

use App\Core\Html;
use App\Core\Url;

$siteTitle = $app->setting('site_title');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= Html::e($app->hooks->applyFilters('seo_title', $page['title'], $page)) ?></title>

    <?php // SEO 外掛（若已啟用）在此輸出 description / canonical / Open Graph / JSON-LD ?>
    <?php $app->hooks->doAction('head_meta', $page); ?>

    <link rel="stylesheet" href="<?= Html::e($theme->parentStyleUrl()) ?>">
    <link rel="stylesheet" href="<?= Html::e($theme->styleUrl()) ?>">
    <link rel="icon" href="<?= Html::e(Url::asset('assets/favicon.svg')) ?>" type="image/svg+xml">
</head>
<body class="<?= Html::e($page['bodyClass']) ?>">
    <?php // 跳過導覽、直接前往主要內容（WCAG 2.4.1） ?>
    <a class="skip-link" href="#primary">Skip to main content</a>

    <?php $theme->partial('partials/header', ['app' => $app, 'page' => $page]); ?>

    <?php /* 頁面切換時只置換 #primary 的內容 */ ?>
    <main id="primary" class="site-main" data-page-title="<?= Html::e($page['title']) ?>">
        <?= $content ?>
    </main>

    <?php $theme->partial('partials/footer', ['app' => $app, 'page' => $page]); ?>

    <?php // 供 JavaScript 讀取的即時區域，播報頁面切換結果（WCAG 4.1.3） ?>
    <p id="route-announcer" class="screen-reader-text" role="status" aria-live="polite"></p>

    <script src="<?= Html::e(Url::asset('assets/js/app.js')) ?>" defer></script>
    <?php $app->hooks->doAction('footer_scripts', $page); ?>
</body>
</html>
