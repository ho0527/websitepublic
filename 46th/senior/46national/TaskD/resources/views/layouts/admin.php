<?php
/**
 * 後台共用版型：左側為各管理功能的快速連結。
 *
 * @var \App\Core\View $view
 * @var string         $content
 * @var string         $title
 * @var string         $currentPath
 */

$adminMenuItems = [
    'admin'             => '後台首頁',
    'admin/train-types' => '車種管理',
    'admin/trains'      => '列車管理',
    'admin/bookings'    => '訂票紀錄查詢',
];
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $view->e($title ?? '後台管理') ?> - 列車訂票系統後台</title>
    <link rel="icon" href="<?= $view->e($view->asset('img/favicon.svg')) ?>">
    <link rel="stylesheet" href="<?= $view->e($view->asset('css/app.css')) ?>">
    <?= $view->partial('partials/theme-boot') ?>
</head>
<body>
    <?= $view->partial('partials/header') ?>

    <div class="page">
        <div class="admin-shell">
            <aside class="admin-menu">
                <strong>管理功能</strong>
                <?php foreach ($adminMenuItems as $path => $label): ?>
                    <?php
                    $isActive = $path === 'admin'
                        ? $currentPath === '/admin'
                        : str_starts_with($currentPath, '/' . $path);
                    ?>
                    <a href="<?= $view->e($view->url($path)) ?>"
                       class="<?= $isActive ? 'is-active' : '' ?>"><?= $view->e($label) ?></a>
                <?php endforeach; ?>
            </aside>

            <main>
                <?= $content ?>
            </main>
        </div>
    </div>

    <footer class="site-footer">
        第 46 屆全國技能競賽 · 17 網頁設計 · 模組 D 列車訂票系統
    </footer>

    <script src="<?= $view->e($view->asset('js/theme.js')) ?>"></script>
</body>
</html>
