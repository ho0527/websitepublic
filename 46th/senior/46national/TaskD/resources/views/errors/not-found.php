<?php
/**
 * 404 找不到頁面。
 *
 * @var \App\Core\View $view
 */
?>
<h1 class="page-title">找不到頁面</h1>
<p class="page-subtitle">您要找的頁面不存在，或連結已經失效。</p>

<div class="card">
    <div class="empty-state">請由上方導覽列選擇要使用的功能。</div>

    <div class="button-row">
        <a class="button" href="<?= $view->e($view->url('')) ?>">回首頁</a>
    </div>
</div>
