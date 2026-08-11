<?php
/**
 * 父主題頁首（無設計的最小實作）；子主題會覆寫此檔。
 *
 * @var \App\Core\App $app
 */

use App\Core\Html;
use App\Core\Url;
?>
<header class="site-header">
    <a href="<?= Html::e(Url::to('')) ?>"><?= Html::e($app->setting('site_title')) ?></a>
    <p><?= Html::e($app->setting('site_tagline')) ?></p>
    <nav aria-label="Main">
        <ul>
            <?php foreach ($app->menu() as $item): ?>
                <li><a href="<?= Html::e($item['url']) ?>"><?= Html::e($item['label']) ?></a></li>
            <?php endforeach; ?>
        </ul>
    </nav>
</header>
