<?php
/**
 * 前台共用版型。
 *
 * @var \App\Core\View    $view
 * @var \App\Core\Request $request
 * @var string            $content 已渲染完成的頁面內容
 * @var string            $title
 */
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $view->e($title ?? '列車訂票系統') ?> - 列車訂票系統</title>
    <link rel="icon" href="<?= $view->e($view->asset('img/favicon.svg')) ?>">
    <link rel="stylesheet" href="<?= $view->e($view->asset('css/app.css')) ?>">
    <?= $view->partial('partials/theme-boot') ?>
</head>
<body>
    <?= $view->partial('partials/header') ?>

    <main class="page<?= !empty($narrow) ? ' page-narrow' : '' ?>">
        <?= $content ?>
    </main>

    <footer class="site-footer">
        第 46 屆全國技能競賽 · 17 網頁設計 · 模組 D 列車訂票系統
    </footer>

    <script src="<?= $view->e($view->asset('js/theme.js')) ?>"></script>
</body>
</html>
