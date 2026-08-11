<?php
/**
 * 外掛：Footer Social Links
 *
 * 於頁尾輸出 Twitter / Facebook / Instagram 連結。
 * 三個網址皆存放於資料庫 settings，可在後台「Settings → Social links」修改。
 */

declare(strict_types=1);

use App\Core\App;
use App\Core\Html;
use App\Core\PluginManager;

return static function (PluginManager $hooks, App $app): void {
    $hooks->addAction('footer_social', static function () use ($app): void {
        // 圖示以行內 SVG 提供，不依賴任何外部 CDN
        $networks = [
            'Twitter' => [
                'url'  => $app->setting('social_twitter'),
                'path' => 'M23 4.9a8.7 8.7 0 0 1-2.5.7 4.4 4.4 0 0 0 1.9-2.4c-.8.5-1.8.9-2.8 1.1a4.4 4.4 0 0 0-7.5 4A12.4 12.4 0 0 1 3 3.7a4.4 4.4 0 0 0 1.4 5.9c-.7 0-1.4-.2-2-.5v.1c0 2.1 1.5 3.9 3.5 4.3-.6.2-1.3.2-2 .1a4.4 4.4 0 0 0 4.1 3 8.9 8.9 0 0 1-6.5 1.8A12.5 12.5 0 0 0 8.3 20c8.1 0 12.6-6.8 12.6-12.6v-.6A9 9 0 0 0 23 4.9Z',
            ],
            'Facebook' => [
                'url'  => $app->setting('social_facebook'),
                'path' => 'M22 12a10 10 0 1 0-11.6 9.9v-7H8v-2.9h2.4V9.8c0-2.4 1.4-3.7 3.6-3.7 1 0 2.1.2 2.1.2v2.3h-1.2c-1.2 0-1.5.7-1.5 1.5v1.8h2.6l-.4 2.9h-2.2v7A10 10 0 0 0 22 12Z',
            ],
            'Instagram' => [
                'url'  => $app->setting('social_instagram'),
                'path' => 'M12 2.2c3.2 0 3.6 0 4.9.1 1.2.1 1.8.3 2.2.4.6.2 1 .5 1.4.9.4.4.7.8.9 1.4.2.4.4 1 .4 2.2.1 1.3.1 1.7.1 4.9s0 3.6-.1 4.9c-.1 1.2-.3 1.8-.4 2.2-.2.6-.5 1-.9 1.4-.4.4-.8.7-1.4.9-.4.2-1 .4-2.2.4-1.3.1-1.7.1-4.9.1s-3.6 0-4.9-.1c-1.2-.1-1.8-.3-2.2-.4-.6-.2-1-.5-1.4-.9a3.7 3.7 0 0 1-.9-1.4c-.2-.4-.4-1-.4-2.2C2.2 15.6 2.2 15.2 2.2 12s0-3.6.1-4.9c.1-1.2.3-1.8.4-2.2.2-.6.5-1 .9-1.4.4-.4.8-.7 1.4-.9.4-.2 1-.4 2.2-.4 1.3-.1 1.7-.1 4.8-.1Zm0 5.6a4.2 4.2 0 1 0 0 8.4 4.2 4.2 0 0 0 0-8.4Zm0 6.9a2.7 2.7 0 1 1 0-5.4 2.7 2.7 0 0 1 0 5.4Zm5.3-7a1 1 0 1 1-2 0 1 1 0 0 1 2 0Z',
            ],
        ];
        ?>
        <nav class="social-links" aria-label="Social media">
            <p class="social-links__title">Follow the museums</p>
            <ul>
                <?php foreach ($networks as $name => $network): ?>
                    <?php if (trim($network['url']) === '') { continue; } ?>
                    <li>
                        <a class="social-links__item" href="<?= Html::e($network['url']) ?>"
                           rel="noopener noreferrer" target="_blank">
                            <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" focusable="false">
                                <path d="<?= Html::e($network['path']) ?>" fill="currentColor"/>
                            </svg>
                            <span class="social-links__label"><?= Html::e($name) ?></span>
                            <span class="screen-reader-text">(opens in a new tab)</span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
        <?php
    });
};
