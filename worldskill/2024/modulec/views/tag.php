<?php
/**
 * 模組 C - 標籤查詢結果頁
 *
 * $viewData:
 *   tagSlug string 網址上的標籤（已 slug 化）
 *   pages   array  含有該標籤的文章
 *   tags    array  全站標籤統計
 */

declare(strict_types=1);

/** @var array $viewData */
$tagSlug = (string) $viewData['tagSlug'];
$pages = $viewData['pages'];
$tags = $viewData['tags'] ?? [];
?>
<div class="wrapper">

    <nav class="breadcrumb" aria-label="Breadcrumb">
        <ol>
            <li><a href="<?= mc_e(mc_url()) ?>">Home</a></li>
            <li><span aria-current="page">Tag: <?= mc_e($tagSlug === '' ? 'all tags' : $tagSlug) ?></span></li>
        </ol>
    </nav>

    <h1 class="listing-title">
        <?= $tagSlug === '' ? 'All tags' : '#' . mc_e($tagSlug) ?>
    </h1>

    <?php if ($tagSlug === ''): ?>
        <p class="listing-intro">Pick a tag to filter the heritage pages.</p>
        <ul class="tag-list tag-list--cloud">
            <?php foreach ($tags as $tag): ?>
                <li>
                    <a class="tag" href="<?= mc_e($tag['url']) ?>">#<?= mc_e($tag['label']) ?>
                        <span class="tag__count"><?= mc_e((string) $tag['count']) ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="listing-intro">
            <?= mc_e((string) count($pages)) ?> page(s) tagged with
            <strong><?= mc_e($tagSlug) ?></strong>.
        </p>

        <?php if ($pages === []): ?>
            <p class="empty-state">No published page carries this tag.</p>
        <?php else: ?>
            <ul class="page-list">
                <?php foreach ($pages as $cardPage): ?>
                    <?php require __DIR__ . '/partials/page-card.php'; ?>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    <?php endif; ?>

</div>
