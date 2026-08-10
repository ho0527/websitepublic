<?php
/**
 * 模組 C - 版型
 *
 * 由 index.php 準備好下列變數後 include：
 *   $viewFile        要嵌入的內容樣板路徑
 *   $viewData        傳給內容樣板的資料
 *   $metaTitle       瀏覽器標題
 *   $metaDescription 頁面描述（同時用於社群分享）
 *   $metaImage       社群分享縮圖網址（絕對網址）
 *   $metaUrl         本頁絕對網址
 *   $metaType        Open Graph 類型：website 或 article
 *   $bodyClass       <body> 的樣式類別
 */

declare(strict_types=1);

/** @var string $viewFile */
/** @var array $viewData */
$metaTitle = $metaTitle ?? MC_SITE_NAME;
$metaDescription = $metaDescription ?? MC_SITE_DESCRIPTION;
$metaImage = $metaImage ?? mc_absolute_url(mc_image_url('lyon-skyline.jpeg'));
$metaUrl = $metaUrl ?? mc_absolute_url(mc_url());
$metaType = $metaType ?? 'website';
$bodyClass = $bodyClass ?? 'page-listing';
$searchQuery = isset($_GET['q']) && is_string($_GET['q']) ? $_GET['q'] : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= mc_e($metaTitle) ?></title>
    <meta name="description" content="<?= mc_e($metaDescription) ?>">

    <!-- 社群分享 meta 標籤（Open Graph / Twitter Card），內容隨每一篇文章動態產生 -->
    <meta property="og:site_name" content="<?= mc_e(MC_SITE_NAME) ?>">
    <meta property="og:title" content="<?= mc_e($metaTitle) ?>">
    <meta property="og:type" content="<?= mc_e($metaType) ?>">
    <meta property="og:url" content="<?= mc_e($metaUrl) ?>">
    <meta property="og:image" content="<?= mc_e($metaImage) ?>">
    <meta property="og:image:alt" content="<?= mc_e($metaTitle) ?>">
    <meta property="og:description" content="<?= mc_e($metaDescription) ?>">
    <meta property="og:locale" content="en_GB">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= mc_e($metaTitle) ?>">
    <meta name="twitter:description" content="<?= mc_e($metaDescription) ?>">
    <meta name="twitter:image" content="<?= mc_e($metaImage) ?>">
    <meta name="twitter:image:alt" content="<?= mc_e($metaTitle) ?>">

    <link rel="canonical" href="<?= mc_e($metaUrl) ?>">
    <link rel="stylesheet" href="<?= mc_e(mc_asset_url('css/style.css')) ?>">
</head>

<body class="<?= mc_e($bodyClass) ?>">
    <a class="skip-link" href="#main-content">Skip to main content</a>

    <header class="site-header">
        <div class="site-header__inner">
            <p class="site-header__brand">
                <a href="<?= mc_e(mc_url()) ?>"><?= mc_e(MC_SITE_NAME) ?></a>
                <span class="site-header__tagline"><?= mc_e(MC_SITE_TAGLINE) ?></span>
            </p>

            <form class="search-form" role="search" method="get" action="<?= mc_e(MC_CLEAN_URL ? mc_url('search') : mc_base_url() . 'index.php') ?>">
                <?php if (!MC_CLEAN_URL): ?>
                    <input type="hidden" name="route" value="search">
                <?php endif; ?>
                <label class="search-form__label" for="site-search">Search pages</label>
                <input class="search-form__input" type="search" id="site-search" name="q"
                       value="<?= mc_e($searchQuery) ?>"
                       placeholder="e.g. mosaic/park" autocomplete="off">
                <button class="search-form__button" type="submit">Search</button>
            </form>
        </div>
    </header>

    <main id="main-content">
        <?php require $viewFile; ?>
    </main>

    <footer class="site-footer">
        <p>
            <?= mc_e(MC_SITE_NAME) ?> — WorldSkills 2024 Web Technologies, Module C.
            Content is read directly from the <code>content-pages</code> folder; no database is used.
        </p>
    </footer>

    <script src="<?= mc_e(mc_asset_url('js/app.js')) ?>"></script>
</body>

</html>
