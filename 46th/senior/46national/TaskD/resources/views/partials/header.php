<?php
/**
 * 頁首：左上角 LOGO 與右上方的常用功能導覽列。
 *
 * @var \App\Core\View $view
 * @var string         $currentPath
 * @var bool           $isAdminSignedIn
 */

// 導覽列項目：路徑 => 顯示名稱
$navigationItems = [
    ''            => '首頁',
    'booking'     => '預訂車票',
    'bookings'    => '訂票查詢',
    'train-info'  => '列車資訊',
    'statistics'  => '資料統計',
];

/**
 * 判斷導覽項目是否為目前所在頁面。
 */
$isActive = static function (string $path) use ($currentPath): bool {
    if ($path === '') {
        return $currentPath === '/' || str_starts_with($currentPath, '/train-lookup');
    }

    return $currentPath === '/' . $path || str_starts_with($currentPath, '/' . $path . '/');
};
?>
<header class="site-header">
    <a class="site-logo" href="<?= $view->e($view->url('')) ?>">
        <?= $view->partial('partials/logo') ?>
    </a>

    <nav class="site-nav">
        <?php foreach ($navigationItems as $path => $label): ?>
            <a href="<?= $view->e($view->url($path)) ?>"
               class="<?= $isActive($path) ? 'is-active' : '' ?>"><?= $view->e($label) ?></a>
        <?php endforeach; ?>

        <?php if ($isAdminSignedIn): ?>
            <a href="<?= $view->e($view->url('admin')) ?>"
               class="<?= str_starts_with($currentPath, '/admin') ? 'is-active' : '' ?>">後台管理</a>
            <form method="post" action="<?= $view->e($view->url('logout')) ?>">
                <button type="submit" class="nav-signout">登出</button>
            </form>
        <?php else: ?>
            <a href="<?= $view->e($view->url('login')) ?>"
               class="<?= $currentPath === '/login' ? 'is-active' : '' ?>">登入後台</a>
        <?php endif; ?>

        <button type="button" class="theme-toggle" id="theme-toggle" aria-label="切換為淺色模式">
            <!-- 深色佈景時顯示月亮，點擊後換成淺色 -->
            <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/>
            </svg>
            <!-- 淺色佈景時顯示太陽 -->
            <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="12" cy="12" r="4"/>
                <path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/>
            </svg>
        </button>
    </nav>
</header>
