<?php
/**
 * 後台登入頁（白標）
 *
 * 規格重點：
 *   - 不顯示任何內容管理系統的標誌
 *   - 頁面上不出現內容管理系統的名稱字樣
 *   - 背景為滿版的博物館照片（可於後台 Settings 更換）
 *   - 網址為 <host>/admin/
 *
 * @var \App\Core\App $app
 * @var string        $error
 * @var string        $username
 * @var string        $background
 * @var bool          $lockedOut
 */

use App\Core\Csrf;
use App\Core\Html;
use App\Core\Url;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Sign in — <?= Html::e($app->setting('site_title')) ?></title>
    <link rel="stylesheet" href="<?= Html::e(Url::asset('assets/css/admin.css')) ?>">
    <link rel="icon" href="<?= Html::e(Url::asset('assets/favicon.svg')) ?>" type="image/svg+xml">
</head>
<body class="login-page">
    <?php // 滿版博物館照片背景 ?>
    <div class="login-background"
         style="background-image:url('<?= Html::e(Url::asset($background)) ?>')"
         role="img"
         aria-label="Photograph of a museum in Kazan"></div>
    <div class="login-scrim" aria-hidden="true"></div>

    <main class="login-card">
        <h1 class="login-card__title"><?= Html::e($app->setting('site_title')) ?></h1>
        <p class="login-card__sub">Staff sign in</p>

        <?php if ($error !== ''): ?>
            <p class="notice notice--error" role="alert"><?= Html::e($error) ?></p>
        <?php endif; ?>

        <form method="post" action="<?= Html::e(Url::to('admin')) ?>" class="login-form">
            <?= Csrf::field() ?>

            <div class="form-row">
                <label class="form-label" for="username">Username</label>
                <input class="form-input" type="text" id="username" name="username"
                       value="<?= Html::e($username) ?>" required autocomplete="username" autofocus>
            </div>

            <div class="form-row">
                <label class="form-label" for="password">Password</label>
                <input class="form-input" type="password" id="password" name="password"
                       required autocomplete="current-password">
            </div>

            <button class="button button--primary button--block" type="submit"
                    <?= $lockedOut ? 'disabled' : '' ?>>Sign in</button>
        </form>

        <?php // 安全性外掛的提示文字 ?>
        <?php $app->hooks->doAction('login_footer'); ?>

        <p class="login-card__back">
            <a href="<?= Html::e(Url::to('')) ?>">Back to the website</a>
        </p>
    </main>
</body>
</html>
