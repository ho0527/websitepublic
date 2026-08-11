<?php
/**
 * 精選博物館頁
 *
 * 規格：整頁大型背景照片（來源為該頁的「精選圖片」featured image），
 *       並顯示該館專屬分類的新聞文章。
 *
 * @var \App\Core\App   $app
 * @var \App\Core\Theme $theme
 * @var array           $museum
 * @var array           $museumPosts
 * @var array           $gallery
 */

use App\Core\Html;
use App\Core\Url;

$background = $museum['featured_image'] !== '' ? Url::asset($museum['featured_image']) : '';
?>
<?php // 整頁背景：以 featured image 作為固定滿版底圖 ?>
<div class="museum-backdrop" role="img"
     aria-label="Photograph of <?= Html::e($museum['title']) ?>"
     style="background-image:url('<?= Html::e($background) ?>')"></div>
<div class="museum-backdrop__scrim" aria-hidden="true"></div>

<article class="museum museum--selected">
    <header class="museum__hero">
        <p class="museum__flag">Selected museum</p>
        <h1 class="museum__title"><?= Html::e($museum['title']) ?></h1>
        <p class="museum__excerpt"><?= Html::e($museum['excerpt']) ?></p>
        <dl class="museum__facts">
            <div>
                <dt>Address</dt>
                <dd><?= Html::e($museum['address']) ?></dd>
            </div>
            <div>
                <dt>Opening hours</dt>
                <dd><?= Html::e($museum['opening_hours']) ?></dd>
            </div>
        </dl>
    </header>

    <div class="museum__panel">
        <div class="museum__content">
            <?= Html::paragraphs($museum['content']) ?>
        </div>

        <?php if (!empty($gallery)): ?>
            <section class="museum__gallery" aria-labelledby="museum-gallery-heading">
                <h2 id="museum-gallery-heading">Gallery</h2>
                <ul>
                    <?php foreach ($gallery as $index => $image): ?>
                        <li>
                            <img src="<?= Html::e(Url::asset($image)) ?>"
                                 alt="<?= Html::e($museum['title']) ?>, photo <?= (int) $index + 1 ?>"
                                 loading="lazy" width="500" height="360">
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endif; ?>

        <?php /* 精選博物館才顯示：只取該館分類的新聞 */ ?>
        <section class="museum__news" aria-labelledby="museum-news-heading">
            <div class="section-head">
                <h2 class="section-head__title" id="museum-news-heading">
                    News from <?= Html::e($museum['title']) ?>
                </h2>
                <?php if (!empty($museum['category_slug'])): ?>
                    <a class="section-head__more" href="<?= Html::e(Url::to('news/' . $museum['category_slug'])) ?>">
                        All posts in this category
                    </a>
                <?php endif; ?>
            </div>

            <?php if (empty($museumPosts)): ?>
                <p class="empty-state">There is no news from this museum yet. Please check back soon.</p>
            <?php else: ?>
                <div class="post-list post-list--two">
                    <?php foreach ($museumPosts as $post): ?>
                        <?php $theme->partial('partials/post-card', ['post' => $post, 'variant' => 'default']); ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <p class="museum__back">
            <a class="button button--ghost" href="<?= Html::e(Url::to('museums')) ?>">Back to all museums</a>
        </p>
    </div>
</article>
