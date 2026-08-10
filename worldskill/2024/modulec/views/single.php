<?php
/**
 * 模組 C - 單篇遺產頁
 *
 * 版面依規格書的示意圖分成四塊：封面圖片、標題、附註資訊、主要內容。
 *
 * $viewData:
 *   page ContentPage 目前文章
 *   html string      已渲染完成的主要內容
 */

declare(strict_types=1);

/** @var array $viewData */
/** @var ContentPage $page */
$page = $viewData['page'];
$contentHtml = (string) $viewData['html'];
$folderPath = $page->folder();
?>
<article class="heritage">

    <!-- 封面圖片：徑向漸層遮罩會跟著滑鼠移動，形成聚光效果（見 assets/js/app.js） -->
    <div class="cover" data-spotlight>
        <div class="cover__frame">
            <!-- 底層：稍微壓暗的封面 -->
            <img class="cover__image" src="<?= mc_e($page->coverImageUrl()) ?>"
                 alt="Cover image of <?= mc_e($page->title()) ?>" width="1600" height="900">
            <!-- 上層：套用徑向漸層遮罩的同一張封面，形成跟隨滑鼠的聚光圈 -->
            <img class="cover__image cover__image--spotlight" src="<?= mc_e($page->coverImageUrl()) ?>"
                 alt="" aria-hidden="true" width="1600" height="900">
        </div>

        <!-- 標題區塊：使用 common-ligatures 連字排版設定 -->
        <h1 class="cover__title"><?= mc_e($page->title()) ?></h1>
    </div>

    <div class="heritage__body wrapper">

        <nav class="breadcrumb" aria-label="Breadcrumb">
            <ol>
                <li><a href="<?= mc_e(mc_url()) ?>">Home</a></li>
                <?php $accumulated = ''; ?>
                <?php foreach ($folderPath === '' ? [] : explode('/', $folderPath) as $segment): ?>
                    <?php $accumulated .= ($accumulated === '' ? '' : '/') . $segment; ?>
                    <li>
                        <a href="<?= mc_e(mc_url('heritages/' . $accumulated)) ?>"><?= mc_e(mc_folder_label($segment)) ?></a>
                    </li>
                <?php endforeach; ?>
                <li><span aria-current="page"><?= mc_e($page->title()) ?></span></li>
            </ol>
        </nav>

        <div class="heritage__grid">

            <!-- 主要內容：由檔案動態載入後渲染 -->
            <div class="article-body" data-lightbox-scope>
                <?= $contentHtml ?>
            </div>

            <!-- 附註資訊：捲動時固定在頂端 -->
            <aside class="heritage-aside" aria-label="Page information">
                <dl class="heritage-aside__list">
                    <?php if ($page->date() !== null): ?>
                        <div class="heritage-aside__row">
                            <dt>Date</dt>
                            <dd><time datetime="<?= mc_e((string) $page->date()) ?>"><?= mc_e((string) $page->date()) ?></time></dd>
                        </div>
                    <?php endif; ?>

                    <?php if ($page->tags() !== []): ?>
                        <div class="heritage-aside__row">
                            <dt>Tags</dt>
                            <dd>
                                <ul class="tag-list">
                                    <?php foreach ($page->tags() as $tag): ?>
                                        <li>
                                            <a class="tag" href="<?= mc_e(mc_url('tags/' . mc_slugify($tag))) ?>"><?= mc_e($tag) ?></a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </dd>
                        </div>
                    <?php endif; ?>

                    <?php if ($page->isDraft()): ?>
                        <div class="heritage-aside__row">
                            <dt>Draft</dt>
                            <dd>true</dd>
                        </div>
                    <?php endif; ?>

                    <div class="heritage-aside__row">
                        <dt>Source file</dt>
                        <dd><code><?= mc_e('content-pages/' . $page->relativePath()) ?></code></dd>
                    </div>
                </dl>
            </aside>

        </div>
    </div>
</article>

<!-- 圖片放大檢視：點擊內容中的照片開啟，再次點擊或捲動即關閉 -->
<div class="lightbox" id="lightbox" role="dialog" aria-modal="true" aria-label="Enlarged photo" hidden>
    <img class="lightbox__image" id="lightbox-image" src="" alt="">
    <button class="lightbox__close" type="button" id="lightbox-close">Close</button>
</div>
