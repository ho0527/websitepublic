<?php
/**
 * 子主題頁尾：版權（年份依伺服器時間動態產生）與社群連結
 *
 * 社群連結由「Footer Social Links」外掛透過 footer_social 掛鉤輸出，
 * 網址可在後台「Settings」修改。
 *
 * @var \App\Core\App $app
 */

use App\Core\Html;
use App\Core\Url;
?>
<footer class="site-footer" role="contentinfo">
    <div class="site-footer__inner">
        <div class="site-footer__brand">
            <p class="site-footer__title"><?= Html::e($app->setting('site_title')) ?></p>
            <p class="site-footer__tagline"><?= Html::e($app->setting('site_tagline')) ?></p>
            <p class="site-footer__owner"><?= Html::e($app->setting('copyright_owner')) ?>, Kazan, Republic of Tatarstan</p>
        </div>

        <nav class="site-footer__links" aria-label="Footer">
            <ul>
                <li><a href="<?= Html::e(Url::to('')) ?>">Home</a></li>
                <li><a href="<?= Html::e(Url::to('museums')) ?>">Museums</a></li>
                <li><a href="<?= Html::e(Url::to('news')) ?>">News</a></li>
                <li><a href="<?= Html::e(Url::to('news/seasonal-events')) ?>">Seasonal Events</a></li>
                <li><a href="<?= Html::e(Url::to('contact')) ?>">Contact</a></li>
            </ul>
        </nav>

        <?php // 外掛掛鉤：頁尾社群連結 ?>
        <?php $app->hooks->doAction('footer_social'); ?>
    </div>

    <div class="site-footer__bar">
        <p class="site-footer__copyright">
            <?php // 規格要求文字為 “Copyright © 2019 - All rights reserved”，年份取伺服器時間 ?>
            Copyright &copy; <?= date('Y') ?> - All rights reserved
        </p>
        <p class="site-footer__admin">
            <a href="<?= Html::e(Url::to('admin')) ?>">Staff login</a>
        </p>
    </div>
</footer>
