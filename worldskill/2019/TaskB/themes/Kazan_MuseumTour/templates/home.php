<?php
/**
 * 首頁
 *
 * 版面策略：
 *   桌機 — 主要區塊為左右兩欄，左半邊是新聞、右半邊是封面大圖與圖片牆
 *   行動 — 各區塊改為上下堆疊、佔滿整個寬度；唯獨「精選博物館」維持左右兩欄
 *
 * @var \App\Core\App   $app
 * @var \App\Core\Theme $theme
 * @var array           $latestPosts
 * @var array           $selectedMuseums
 * @var array           $otherMuseums
 * @var array           $seasonalPosts
 * @var string          $coverImage
 * @var array           $galleryImages
 */

use App\Core\Html;
use App\Core\Url;
?>
<section class="hero">
    <div class="hero__inner">
        <p class="hero__eyebrow">Kazan · Republic of Tatarstan</p>
        <h1 class="hero__title"><?= Html::e($app->setting('site_title')) ?></h1>
        <p class="hero__tagline"><?= Html::e($app->setting('site_tagline')) ?></p>
        <p class="hero__lead"><?= Html::e($app->setting('site_description')) ?></p>
        <p class="hero__actions">
            <a class="button button--primary" href="<?= Html::e(Url::to('museums')) ?>">Browse all museums</a>
            <a class="button button--ghost" href="<?= Html::e(Url::to('news/seasonal-events')) ?>">What is on this season</a>
        </p>
    </div>
</section>

<?php /* 桌機兩欄：左新聞 / 右封面與圖片 */ ?>
<div class="home-columns">
    <section class="home-columns__news" aria-labelledby="home-news-heading">
        <div class="section-head">
            <h2 class="section-head__title" id="home-news-heading">Latest news</h2>
            <a class="section-head__more" href="<?= Html::e(Url::to('news')) ?>">All news</a>
        </div>
        <p class="section-head__note">Fresh posts from every category — site updates, seasonal events and the selected museums.</p>

        <div class="post-list">
            <?php foreach ($latestPosts as $post): ?>
                <?php $theme->partial('partials/post-card', ['post' => $post, 'variant' => 'default']); ?>
            <?php endforeach; ?>
        </div>
    </section>

    <aside class="home-columns__cover" aria-labelledby="home-cover-heading">
        <h2 class="screen-reader-text" id="home-cover-heading">Kazan in pictures</h2>

        <?php if ($coverImage !== ''): ?>
            <figure class="cover-figure">
                <img src="<?= Html::e(Url::asset($coverImage)) ?>"
                     alt="Panorama of Kazan with the Kremlin and the Kazanka river"
                     width="800" height="1000">
                <figcaption>
                    <strong>Eight museums, one city.</strong>
                    From a Soviet communal apartment to the treasures of Volga Bulgaria — all within walking distance of the Kremlin.
                </figcaption>
            </figure>
        <?php endif; ?>

        <?php if (!empty($galleryImages)): ?>
            <ul class="cover-gallery">
                <?php foreach ($galleryImages as $index => $image): ?>
                    <li>
                        <img src="<?= Html::e(Url::asset($image)) ?>"
                             alt="View of Kazan, photo <?= (int) $index + 1 ?>"
                             loading="lazy" width="400" height="300">
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if (!empty($seasonalPosts)): ?>
            <section class="cover-events" aria-labelledby="home-events-heading">
                <h3 class="cover-events__title" id="home-events-heading">Upcoming events</h3>
                <ul class="event-list">
                    <?php foreach ($seasonalPosts as $event): ?>
                        <li class="event-list__item">
                            <time class="event-list__date" datetime="<?= Html::e(Html::date($event['published_at'], 'Y-m-d')) ?>">
                                <span class="event-list__day"><?= Html::e(Html::date($event['published_at'], 'd')) ?></span>
                                <span class="event-list__month"><?= Html::e(Html::date($event['published_at'], 'M')) ?></span>
                            </time>
                            <a href="<?= Html::e(Url::to('news/' . $event['category_slug'] . '/' . $event['slug'])) ?>">
                                <?= Html::e($event['title']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <a class="button button--ghost button--small" href="<?= Html::e(Url::to('news/seasonal-events')) ?>">
                    All seasonal events
                </a>
            </section>
        <?php endif; ?>
    </aside>
</div>

<?php /* 精選博物館：行動裝置維持左右兩欄 */ ?>
<section class="selected-museums" aria-labelledby="selected-museums-heading">
    <div class="section-head">
        <h2 class="section-head__title" id="selected-museums-heading">Selected museums</h2>
        <a class="section-head__more" href="<?= Html::e(Url::to('museums')) ?>">See all</a>
    </div>
    <p class="section-head__note">Four museums with their own news feed and their own story to tell.</p>

    <div class="museum-grid museum-grid--two">
        <?php foreach ($selectedMuseums as $museum): ?>
            <?php $theme->partial('partials/museum-card', ['museum' => $museum]); ?>
        <?php endforeach; ?>
    </div>
</section>

<section class="other-museums" aria-labelledby="other-museums-heading">
    <div class="section-head">
        <h2 class="section-head__title" id="other-museums-heading">More to discover</h2>
    </div>
    <ul class="museum-list">
        <?php foreach ($otherMuseums as $museum): ?>
            <li class="museum-list__item">
                <a href="<?= Html::e(Url::to($museum['slug'])) ?>">
                    <span class="museum-list__name"><?= Html::e($museum['title']) ?></span>
                    <span class="museum-list__excerpt"><?= Html::e($museum['excerpt']) ?></span>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
