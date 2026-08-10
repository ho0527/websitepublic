<?php
/**
 * WorldSkills 2024 - Web Technologies 模組 C「Lyon Heritage Sites」
 *
 * 單一入口控制器：解析路由 → 取得資料 → 選擇樣板 → 交給版型輸出。
 * 本專案不使用資料庫，所有內容都直接讀取 content-pages 資料夾內的 .html / .txt 檔案。
 *
 * 支援的路由：
 *   /                                     索引列表
 *   /heritages/{slug}                     content-pages 根層的單篇文章
 *   /heritages/{folder}                   子資料夾列表
 *   /heritages/{folder}/{slug}            子資料夾內的單篇文章
 *   /heritages/{folder}/{folder2}/{slug}  巢狀子資料夾內的單篇文章
 *   /tags/{tag}                           標籤查詢
 *   /search?q={keyword}                   標題與內容搜尋（"/" 可分隔多個關鍵字，OR 邏輯）
 */

declare(strict_types=1);

require_once __DIR__ . '/app/config.php';
require_once __DIR__ . '/app/helpers.php';
require_once __DIR__ . '/app/Router.php';
require_once __DIR__ . '/app/ContentPage.php';
require_once __DIR__ . '/app/ContentRepository.php';
require_once __DIR__ . '/app/ContentRenderer.php';

$repository = new ContentRepository(MC_CONTENT_DIR);
$route = Router::currentRoute();
$resolved = Router::resolve($route);

// 預設值：找不到對應頁面時會沿用這一組
$viewFile = __DIR__ . '/views/not-found.php';
$viewData = ['path' => $resolved['path']];
$metaTitle = MC_SITE_NAME;
$metaDescription = MC_SITE_DESCRIPTION;
$metaImage = mc_absolute_url(mc_image_url('lyon-skyline.jpeg'));
$metaUrl = mc_absolute_url(mc_url($route));
$metaType = 'website';
$bodyClass = 'page-listing';

switch ($resolved['type']) {

    // ------------------------------------------------------------------
    // 索引列表（content-pages 最上層）
    // ------------------------------------------------------------------
    case 'index':
        $listing = $repository->listFolder('');
        $viewFile = __DIR__ . '/views/listing.php';
        $viewData = [
            'folderPath' => '',
            'folders' => $listing['folders'],
            'pages' => $listing['pages'],
            'tags' => $repository->allTags(),
        ];
        $metaTitle = MC_SITE_NAME . ' — ' . MC_SITE_TAGLINE;
        break;

    // ------------------------------------------------------------------
    // /heritages/... 可能是子資料夾列表，也可能是單篇文章
    // ------------------------------------------------------------------
    case 'page-or-folder':
        $path = $resolved['path'];

        if ($repository->isFolder($path)) {
            // 子資料夾 → 列出其中的頁面與子資料夾
            $listing = $repository->listFolder($path);
            $viewFile = __DIR__ . '/views/listing.php';
            $viewData = [
                'folderPath' => trim($path, '/'),
                'folders' => $listing['folders'],
                'pages' => $listing['pages'],
                'tags' => [],
            ];
            $metaTitle = mc_folder_label(basename($path)) . ' — ' . MC_SITE_NAME;
            $metaDescription = 'Heritage pages stored in the ' . trim($path, '/') . ' folder.';
            break;
        }

        $page = $repository->findPage($path);
        if ($page === null) {
            http_response_code(404);
            break;
        }

        // 單篇文章：主要內容依副檔名動態渲染
        $viewFile = __DIR__ . '/views/single.php';
        $viewData = [
            'page' => $page,
            'html' => ContentRenderer::render($page),
        ];
        $metaTitle = $page->title() . ' — ' . MC_SITE_NAME;
        $metaDescription = $page->summary() !== '' ? $page->summary() : MC_SITE_DESCRIPTION;
        $metaImage = mc_absolute_url($page->coverImageUrl());
        $metaUrl = mc_absolute_url($page->url());
        $metaType = 'article';
        $bodyClass = 'page-heritage';
        break;

    // ------------------------------------------------------------------
    // /tags/{tag} 標籤查詢
    // ------------------------------------------------------------------
    case 'tag':
        $tagSlug = mc_slugify($resolved['path']);
        $viewFile = __DIR__ . '/views/tag.php';
        $viewData = [
            'tagSlug' => $tagSlug,
            'pages' => $tagSlug === '' ? [] : $repository->pagesByTag($tagSlug),
            'tags' => $repository->allTags(),
        ];
        $metaTitle = ($tagSlug === '' ? 'All tags' : 'Tag: ' . $tagSlug) . ' — ' . MC_SITE_NAME;
        $metaDescription = 'Heritage pages tagged with ' . ($tagSlug === '' ? 'any tag' : $tagSlug) . '.';
        break;

    // ------------------------------------------------------------------
    // /search?q=... 搜尋標題與內容
    // ------------------------------------------------------------------
    case 'search':
        $query = isset($_GET['q']) && is_string($_GET['q']) ? trim($_GET['q']) : '';
        $viewFile = __DIR__ . '/views/search.php';
        $viewData = [
            'query' => $query,
            'keywords' => ContentRepository::parseKeywords($query),
            'pages' => $query === '' ? [] : $repository->search($query),
        ];
        $metaTitle = 'Search — ' . MC_SITE_NAME;
        $metaDescription = 'Search the heritage pages by title or content.';
        break;

    // ------------------------------------------------------------------
    // 其他路由一律視為 404
    // ------------------------------------------------------------------
    default:
        http_response_code(404);
        break;
}

require __DIR__ . '/views/layout.php';
