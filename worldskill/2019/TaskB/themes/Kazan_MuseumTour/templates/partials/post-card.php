<?php
/**
 * 新聞卡片
 *
 * @var array  $post     文章資料（含 category_slug / category_name）
 * @var string $variant  版面變化：'default' | 'compact' | 'overlay'
 */

use App\Core\Html;
use App\Core\Url;

$variant  = $variant ?? 'default';
$postUrl  = Url::to('news/' . $post['category_slug'] . '/' . $post['slug']);
$catUrl   = Url::to('news/' . $post['category_slug']);
$image    = $post['featured_image'] !== '' ? Url::asset($post['featured_image']) : '';
$excerpt  = $post['excerpt'] !== '' ? $post['excerpt'] : Html::excerpt($post['content'], 140);
?>
<article class="post-card post-card--<?= Html::e($variant) ?>">
    <?php if ($image !== '' && $variant !== 'compact'): ?>
        <a class="post-card__media" href="<?= Html::e($postUrl) ?>" tabindex="-1" aria-hidden="true">
            <img src="<?= Html::e($image) ?>" alt="" loading="lazy" width="640" height="400">
        </a>
    <?php endif; ?>

    <div class="post-card__body">
        <p class="post-card__meta">
            <a class="post-card__category" href="<?= Html::e($catUrl) ?>"><?= Html::e($post['category_name']) ?></a>
            <time datetime="<?= Html::e(Html::date($post['published_at'], 'Y-m-d')) ?>">
                <?= Html::e(Html::date($post['published_at'])) ?>
            </time>
        </p>
        <h3 class="post-card__title">
            <a href="<?= Html::e($postUrl) ?>"><?= Html::e($post['title']) ?></a>
        </h3>
        <p class="post-card__excerpt"><?= Html::e($excerpt) ?></p>
    </div>
</article>
