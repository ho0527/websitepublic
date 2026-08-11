<?php
/**
 * 單篇新聞頁
 *
 * @var \App\Core\Theme $theme
 * @var array           $post
 * @var array           $siblings  ['prev' => ?array, 'next' => ?array]
 * @var array           $related
 */

use App\Core\Html;
use App\Core\Url;

$image = $post['featured_image'] !== '' ? Url::asset($post['featured_image']) : '';
?>
<article class="single-post">
    <header class="single-post__header">
        <p class="single-post__meta">
            <a class="chip chip--active" href="<?= Html::e(Url::to('news/' . $post['category_slug'])) ?>">
                <?= Html::e($post['category_name']) ?>
            </a>
            <time datetime="<?= Html::e(Html::date($post['published_at'], 'Y-m-d')) ?>">
                <?= Html::e(Html::date($post['published_at'], 'j F Y')) ?>
            </time>
            <?php if (!empty($post['author_name'])): ?>
                <span class="single-post__author">by <?= Html::e($post['author_name']) ?></span>
            <?php endif; ?>
        </p>
        <h1 class="single-post__title"><?= Html::e($post['title']) ?></h1>
        <p class="single-post__lead"><?= Html::e($post['excerpt']) ?></p>
    </header>

    <?php if ($image !== ''): ?>
        <figure class="single-post__figure">
            <img src="<?= Html::e($image) ?>" alt="<?= Html::e($post['title']) ?>" width="1200" height="700">
        </figure>
    <?php endif; ?>

    <div class="single-post__content">
        <?= Html::paragraphs($post['content']) ?>
    </div>

    <nav class="post-nav" aria-label="More posts in this category">
        <?php if (!empty($siblings['prev'])): ?>
            <a class="post-nav__link post-nav__link--prev"
               href="<?= Html::e(Url::to('news/' . $post['category_slug'] . '/' . $siblings['prev']['slug'])) ?>">
                <span class="post-nav__label">Previous</span>
                <span class="post-nav__title"><?= Html::e($siblings['prev']['title']) ?></span>
            </a>
        <?php endif; ?>
        <?php if (!empty($siblings['next'])): ?>
            <a class="post-nav__link post-nav__link--next"
               href="<?= Html::e(Url::to('news/' . $post['category_slug'] . '/' . $siblings['next']['slug'])) ?>">
                <span class="post-nav__label">Next</span>
                <span class="post-nav__title"><?= Html::e($siblings['next']['title']) ?></span>
            </a>
        <?php endif; ?>
    </nav>
</article>

<?php if (!empty($related)): ?>
    <section class="related-posts" aria-labelledby="related-heading">
        <div class="section-head">
            <h2 class="section-head__title" id="related-heading">More from <?= Html::e($post['category_name']) ?></h2>
        </div>
        <div class="post-list post-list--grid">
            <?php foreach ($related as $item): ?>
                <?php $theme->partial('partials/post-card', ['post' => $item, 'variant' => 'default']); ?>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>
