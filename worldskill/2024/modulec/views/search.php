<?php
/**
 * 模組 C - 搜尋結果頁
 *
 * $viewData:
 *   query    string 使用者輸入的原始查詢字串
 *   keywords array  以 "/" 拆開後的關鍵字（OR 邏輯）
 *   pages    array  命中的文章
 */

declare(strict_types=1);

/** @var array $viewData */
$query = (string) $viewData['query'];
$keywords = $viewData['keywords'];
$pages = $viewData['pages'];
?>
<div class="wrapper">

    <nav class="breadcrumb" aria-label="Breadcrumb">
        <ol>
            <li><a href="<?= mc_e(mc_url()) ?>">Home</a></li>
            <li><span aria-current="page">Search</span></li>
        </ol>
    </nav>

    <h1 class="listing-title">Search results</h1>

    <?php if ($query === ''): ?>
        <p class="listing-intro">
            Type a keyword in the search box above. Use <code>/</code> between keywords to search several
            of them at once, for example <code>mosaic/park</code>; the results contain either keyword.
        </p>
    <?php else: ?>
        <p class="listing-intro">
            <?= mc_e((string) count($pages)) ?> page(s) whose title or content match
            <?php foreach ($keywords as $index => $keyword): ?>
                <strong><?= mc_e($keyword) ?></strong><?= $index < count($keywords) - 1 ? ' <em>or</em> ' : '' ?>
            <?php endforeach; ?>.
        </p>

        <?php if ($pages === []): ?>
            <p class="empty-state">Nothing matched this search.</p>
        <?php else: ?>
            <ul class="page-list">
                <?php foreach ($pages as $cardPage): ?>
                    <?php require __DIR__ . '/partials/page-card.php'; ?>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    <?php endif; ?>

</div>
