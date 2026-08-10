<?php
/**
 * 模組 C - 內容列表頁（首頁與各層子資料夾共用）
 *
 * $viewData:
 *   folderPath string 目前資料夾（相對 content-pages），首頁為空字串
 *   folders    array  子資料夾清單（已依字母順序排序）
 *   pages      array  文章清單（已依檔名反向排序，最新在上）
 *   tags       array  全站標籤統計，只在首頁顯示
 */

declare(strict_types=1);

/** @var array $viewData */
$folderPath = (string) $viewData['folderPath'];
$folders = $viewData['folders'];
$pages = $viewData['pages'];
$tags = $viewData['tags'] ?? [];
$isRoot = $folderPath === '';
?>
<div class="wrapper">

    <?php
    // 麵包屑：把資料夾路徑逐段拆開，每一段都連回該層的列表
    $breadcrumbSegments = $isRoot ? [] : explode('/', $folderPath);
    ?>
    <nav class="breadcrumb" aria-label="Breadcrumb">
        <ol>
            <li><a href="<?= mc_e(mc_url()) ?>">Home</a></li>
            <?php $accumulated = ''; ?>
            <?php foreach ($breadcrumbSegments as $index => $segment): ?>
                <?php
                $accumulated .= ($accumulated === '' ? '' : '/') . $segment;
                $isLast = $index === count($breadcrumbSegments) - 1;
                ?>
                <li>
                    <?php if ($isLast): ?>
                        <span aria-current="page"><?= mc_e(mc_folder_label($segment)) ?></span>
                    <?php else: ?>
                        <a href="<?= mc_e(mc_url('heritages/' . $accumulated)) ?>"><?= mc_e(mc_folder_label($segment)) ?></a>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ol>
    </nav>

    <h1 class="listing-title">
        <?= $isRoot ? mc_e('Lyon Heritage Sites') : mc_e(mc_folder_label(basename($folderPath))) ?>
    </h1>

    <p class="listing-intro">
        <?php if ($isRoot): ?>
            Articles written by our editors, stored as plain <code>.html</code> and <code>.txt</code> files.
            Draft, undated and future dated pages are never listed.
        <?php else: ?>
            Pages and sub-folders inside <code><?= mc_e('content-pages/' . $folderPath) ?></code>.
        <?php endif; ?>
    </p>

    <?php if ($folders !== []): ?>
        <section class="folder-section" aria-labelledby="folders-heading">
            <h2 id="folders-heading">Sub-folders</h2>
            <ul class="folder-list">
                <?php foreach ($folders as $folder): ?>
                    <li class="folder-card">
                        <a href="<?= mc_e($folder['url']) ?>">
                            <span class="folder-card__icon" aria-hidden="true">&#128193;</span>
                            <span class="folder-card__name"><?= mc_e($folder['label']) ?></span>
                            <span class="folder-card__count"><?= mc_e((string) $folder['pageCount']) ?> pages</span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    <?php endif; ?>

    <section class="page-section" aria-labelledby="pages-heading">
        <h2 id="pages-heading">Pages</h2>

        <?php if ($pages === []): ?>
            <p class="empty-state">There is no published page in this folder yet.</p>
        <?php else: ?>
            <ul class="page-list">
                <?php foreach ($pages as $cardPage): ?>
                    <?php require __DIR__ . '/partials/page-card.php'; ?>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>

    <?php if ($isRoot && $tags !== []): ?>
        <section class="tag-section" aria-labelledby="tags-heading">
            <h2 id="tags-heading">Browse by tag</h2>
            <ul class="tag-list tag-list--cloud">
                <?php foreach ($tags as $tag): ?>
                    <li>
                        <a class="tag" href="<?= mc_e($tag['url']) ?>">#<?= mc_e($tag['label']) ?>
                            <span class="tag__count"><?= mc_e((string) $tag['count']) ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    <?php endif; ?>

</div>
