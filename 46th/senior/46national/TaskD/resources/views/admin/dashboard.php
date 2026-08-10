<?php
/**
 * 後台首頁：各管理功能的快速連結與概況。
 *
 * @var \App\Core\View $view
 * @var int            $trainTypeCount
 * @var int            $trainCount
 * @var int            $activeBookings
 * @var int            $upcomingBookings
 */
?>
<h1 class="page-title">後台管理</h1>
<p class="page-subtitle">歡迎回來，請由下方或左側選單選擇要使用的管理功能。</p>

<div class="card">
    <h2 class="card-title">系統概況</h2>

    <div class="summary">
        <div>
            <dt>車種數量</dt>
            <dd class="is-large"><?= $view->e($trainTypeCount) ?></dd>
        </div>
        <div>
            <dt>營運中列車</dt>
            <dd class="is-large"><?= $view->e($trainCount) ?></dd>
        </div>
        <div>
            <dt>有效訂票</dt>
            <dd class="is-large"><?= $view->e($activeBookings) ?></dd>
        </div>
        <div>
            <dt>尚未發車的訂票</dt>
            <dd class="is-large"><?= $view->e($upcomingBookings) ?></dd>
        </div>
    </div>
</div>

<div class="card">
    <h2 class="card-title">管理功能</h2>

    <div class="button-row" style="margin-top: 0;">
        <a class="button" href="<?= $view->e($view->url('admin/train-types')) ?>">車種管理</a>
        <a class="button" href="<?= $view->e($view->url('admin/trains')) ?>">列車管理</a>
        <a class="button" href="<?= $view->e($view->url('admin/bookings')) ?>">訂票紀錄查詢</a>
        <a class="button button-secondary" href="<?= $view->e($view->url('statistics')) ?>">搭乘人數統計</a>
    </div>
</div>
