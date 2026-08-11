<?php
/**
 * 子主題頁首：站名／標語 + 置頂主選單（Museums 為下拉選單）
 *
 * @var \App\Core\App $app
 * @var array         $page
 */

use App\Core\Html;
use App\Core\Url;

$menu    = $app->menu();
$current = trim((string) ($page['canonical'] ?? ''), '/');
?>
<header class="site-header" role="banner">
    <div class="site-header__inner">
        <a class="site-brand" href="<?= Html::e(Url::to('')) ?>">
            <span class="site-brand__mark" aria-hidden="true">
                <svg viewBox="0 0 40 40" width="34" height="34" focusable="false">
                    <path d="M20 4 4 13h32L20 4Z" fill="currentColor"/>
                    <rect x="8"  y="16" width="4" height="14" fill="currentColor"/>
                    <rect x="18" y="16" width="4" height="14" fill="currentColor"/>
                    <rect x="28" y="16" width="4" height="14" fill="currentColor"/>
                    <rect x="4"  y="32" width="32" height="4" fill="currentColor"/>
                </svg>
            </span>
            <span class="site-brand__text">
                <span class="site-brand__title"><?= Html::e($app->setting('site_title')) ?></span>
                <span class="site-brand__tagline"><?= Html::e($app->setting('site_tagline')) ?></span>
            </span>
        </a>

        <button class="menu-toggle" type="button"
                aria-expanded="false" aria-controls="primary-menu">
            <span class="menu-toggle__bars" aria-hidden="true"></span>
            <span class="screen-reader-text">Toggle main menu</span>
        </button>

        <nav class="site-nav" id="site-nav" aria-label="Main">
            <ul class="menu" id="primary-menu">
                <?php foreach ($menu as $index => $item): ?>
                    <?php
                        $hasChildren = !empty($item['children']);
                        $isCurrent   = $current === trim((string) $item['route'], '/');
                    ?>
                    <li class="menu-item<?= $hasChildren ? ' menu-item--has-children' : '' ?>">
                        <a href="<?= Html::e($item['url']) ?>"
                           <?= $isCurrent ? 'aria-current="page"' : '' ?>><?= Html::e($item['label']) ?></a>

                        <?php if ($hasChildren): ?>
                            <button class="submenu-toggle" type="button"
                                    aria-expanded="false" aria-controls="submenu-<?= (int) $index ?>">
                                <span aria-hidden="true">▾</span>
                                <span class="screen-reader-text">Show museums</span>
                            </button>
                            <ul class="sub-menu" id="submenu-<?= (int) $index ?>">
                                <?php foreach ($item['children'] as $child): ?>
                                    <li>
                                        <a href="<?= Html::e($child['url']) ?>"
                                           <?= $current === trim((string) $child['route'], '/') ? 'aria-current="page"' : '' ?>>
                                            <?= Html::e($child['label']) ?>
                                            <?php if (!empty($child['selected'])): ?>
                                                <span class="pill" aria-label="Selected museum">★</span>
                                            <?php endif; ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
    </div>
    <?php // 頁面切換進度條（由 assets/js/app.js 控制） ?>
    <div class="route-progress" aria-hidden="true"></div>
</header>
