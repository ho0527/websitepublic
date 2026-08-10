<?php
/**
 * 模組 C - 列表中的單張文章卡片
 *
 * 需要的變數：$cardPage（ContentPage）
 * 標題與摘要都連到同一篇文章，符合「點擊標題或摘要皆可進入」的要求。
 */

declare(strict_types=1);

/** @var ContentPage $cardPage */
$cardUrl = $cardPage->url();
?>
<li class="page-card">
    <?php /* 封面只是裝飾，標題與摘要才是可點擊的連結，因此這裡用空的 alt 讓輔助技術略過 */ ?>
    <div class="page-card__cover">
        <img src="<?= mc_e($cardPage->coverImageUrl()) ?>" alt="" loading="lazy" width="1600" height="900">
    </div>

    <div class="page-card__body">
        <h3 class="page-card__title">
            <a href="<?= mc_e($cardUrl) ?>"><?= mc_e($cardPage->title()) ?></a>
        </h3>

        <p class="page-card__summary">
            <a href="<?= mc_e($cardUrl) ?>"><?= mc_e($cardPage->summary()) ?></a>
        </p>

        <p class="page-card__meta">
            <time datetime="<?= mc_e((string) $cardPage->date()) ?>"><?= mc_e((string) $cardPage->date()) ?></time>
            <span class="page-card__format"><?= mc_e('.' . $cardPage->extension()) ?></span>
        </p>

        <?php if ($cardPage->tags() !== []): ?>
            <ul class="tag-list">
                <?php foreach ($cardPage->tags() as $tag): ?>
                    <li>
                        <a class="tag" href="<?= mc_e(mc_url('tags/' . mc_slugify($tag))) ?>">#<?= mc_e($tag) ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</li>
