<?php
/**
 * 一般博物館頁（未列入精選名單）
 *
 * 規格：使用大型照片橫幅作為頁首（來源為該頁的「精選圖片」featured image），
 *       不顯示該館專屬新聞。
 *
 * @var \App\Core\App   $app
 * @var \App\Core\Theme $theme
 * @var array           $museum
 * @var array           $gallery
 * @var array           $otherMuseums
 */

use App\Core\Html;
use App\Core\Url;

$banner = $museum['featured_image'] !== '' ? Url::asset($museum['featured_image']) : '';
?>
<article class="museum museum--general">
    <?php // 大型照片橫幅：以 featured image 為來源 ?>
    <header class="museum-banner">
        <?php if ($banner !== ''): ?>
            <img class="museum-banner__image" src="<?= Html::e($banner) ?>"
                 alt="<?= Html::e($museum['title']) ?>" width="1600" height="800">
        <?php endif; ?>
        <div class="museum-banner__caption">
            <h1 class="museum-banner__title"><?= Html::e($museum['title']) ?></h1>
            <p class="museum-banner__excerpt"><?= Html::e($museum['excerpt']) ?></p>
        </div>
    </header>

    <div class="museum__layout">
        <div class="museum__content">
            <?= Html::paragraphs($museum['content']) ?>

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
        </div>

        <aside class="museum__aside" aria-label="Visitor information">
            <div class="info-card">
                <h2 class="info-card__title">Plan your visit</h2>
                <dl class="info-card__list">
                    <div>
                        <dt>Address</dt>
                        <dd><?= Html::e($museum['address']) ?></dd>
                    </div>
                    <div>
                        <dt>Opening hours</dt>
                        <dd><?= Html::e($museum['opening_hours']) ?></dd>
                    </div>
                </dl>
                <a class="button button--primary button--small" href="<?= Html::e(Url::to('contact')) ?>">Ask a question</a>
            </div>

            <?php if (!empty($otherMuseums)): ?>
                <div class="info-card">
                    <h2 class="info-card__title">Nearby museums</h2>
                    <ul class="info-card__links">
                        <?php foreach ($otherMuseums as $other): ?>
                            <li><a href="<?= Html::e(Url::to($other['slug'])) ?>"><?= Html::e($other['title']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </aside>
    </div>

    <p class="museum__back">
        <a class="button button--ghost" href="<?= Html::e(Url::to('museums')) ?>">Back to all museums</a>
    </p>
</article>
