<?php
/**
 * 後台登入。
 *
 * @var \App\Core\View     $view
 * @var array<int, string> $errors
 */
?>
<div class="auth-page">
    <h1 class="page-title">登入後台</h1>
    <p class="page-subtitle">請以管理員帳號登入，才能使用後台的各項管理功能。</p>

    <?= $view->partial('partials/alerts', ['errors' => $errors]) ?>

    <div class="card">
        <h2 class="card-title">管理員登入</h2>

        <form method="post" action="<?= $view->e($view->url('login')) ?>">
            <div class="field" style="margin-bottom: 14px;">
                <label for="username">帳號</label>
                <input type="text" id="username" name="username" autocomplete="username" required>
            </div>

            <div class="field">
                <label for="password">密碼</label>
                <input type="password" id="password" name="password" autocomplete="current-password" required>
            </div>

            <div class="button-row">
                <button type="submit" class="button">登入</button>
                <a class="button button-secondary" href="<?= $view->e($view->url('')) ?>">回首頁</a>
            </div>
        </form>
    </div>
</div>
