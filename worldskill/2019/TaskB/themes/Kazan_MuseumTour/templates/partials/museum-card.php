<?php
/**
 * 博物館卡片
 *
 * @var array $museum
 */

use App\Core\Html;
use App\Core\Url;

$museumUrl = Url::to($museum['slug']);
$image     = $museum['featured_image'] !== '' ? Url::asset($museum['featured_image']) : '';
?>
<article class="museum-card<?= (int) $museum['is_selected'] === 1 ? ' museum-card--selected' : '' ?>">
    <a class="museum-card__link" href="<?= Html::e($museumUrl) ?>">
        <?php if ($image !== ''): ?>
            <img class="museum-card__image" src="<?= Html::e($image) ?>"
                 alt="<?= Html::e($museum['title']) ?>" loading="lazy" width="640" height="420">
        <?php endif; ?>
        <span class="museum-card__overlay">
            <?php if ((int) $museum['is_selected'] === 1): ?>
                <span class="museum-card__flag">Selected museum</span>
            <?php endif; ?>
            <span class="museum-card__title"><?= Html::e($museum['title']) ?></span>
            <span class="museum-card__hours"><?= Html::e($museum['opening_hours']) ?></span>
        </span>
    </a>
</article>
