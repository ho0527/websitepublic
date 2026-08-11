<?php
/**
 * 後台版面骨架
 *
 * @var \App\Core\App $app
 * @var string        $title    頁面標題
 * @var string        $content  已渲染的頁面內容
 * @var array         $flash    提示訊息
 */

use App\Core\Html;
use App\Core\Url;

$user       = $app->auth->user();
$currentUrl = trim((string) ($_SERVER['REQUEST_URI'] ?? ''), '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title><?= Html::e($title) ?> — <?= Html::e($app->setting('site_title')) ?> control panel</title>
    <link rel="stylesheet" href="<?= Html::e(Url::asset('assets/css/admin.css')) ?>">
    <link rel="icon" href="<?= Html::e(Url::asset('assets/favicon.svg')) ?>" type="image/svg+xml">
</head>
<body class="admin">
<a class="skip-link" href="#admin-main">Skip to main content</a>

<div class="admin-shell">
    <aside class="admin-sidebar">
        <div class="admin-brand">
            <span class="admin-brand__title"><?= Html::e($app->setting('site_title')) ?></span>
            <span class="admin-brand__sub">Control panel</span>
        </div>

        <nav class="admin-nav" aria-label="Control panel">
            <ul>
                <?php foreach ($app->adminMenu() as $item): ?>
                    <?php
                        $itemUrl   = Url::to($item['route']);
                        $isCurrent = str_ends_with(rtrim($currentUrl, '/'), rtrim(trim($itemUrl, '/'), '/'));
                    ?>
                    <li>
                        <a href="<?= Html::e($itemUrl) ?>" <?= $isCurrent ? 'aria-current="page"' : '' ?>>
                            <span class="admin-nav__icon" aria-hidden="true"><?= Html::e($item['icon']) ?></span>
                            <?= Html::e($item['label']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <div class="admin-user">
            <p class="admin-user__name"><?= Html::e($user['display_name'] ?? '') ?></p>
            <p class="admin-user__role">Role: <?= Html::e($user['role'] ?? '') ?></p>
            <p class="admin-user__actions">
                <a href="<?= Html::e(Url::to('')) ?>" target="_blank" rel="noopener">View site</a>
                <a href="<?= Html::e(Url::to('admin/logout')) ?>">Sign out</a>
            </p>
        </div>
    </aside>

    <div class="admin-content">
        <header class="admin-header">
            <h1 class="admin-header__title"><?= Html::e($title) ?></h1>
        </header>

        <main class="admin-main" id="admin-main">
            <?php foreach ($flash as $message): ?>
                <div class="notice notice--<?= Html::e($message['type']) ?>" role="status">
                    <p><?= Html::e($message['message']) ?></p>
                </div>
            <?php endforeach; ?>

            <?= $content ?>
        </main>
    </div>
</div>

<script src="<?= Html::e(Url::asset('assets/js/admin.js')) ?>" defer></script>
</body>
</html>
