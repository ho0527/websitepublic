<?php
/**
 * 模組 C - 內容倉儲
 *
 * 負責掃描 content-pages 資料夾，把檔案系統的結構轉成頁面與子資料夾清單。
 * 所有「路徑安全性檢查」也集中在這裡，避免使用者用 ../ 讀到資料夾以外的檔案。
 */

declare(strict_types=1);

require_once __DIR__ . '/ContentPage.php';

final class ContentRepository
{
    /** content-pages 的實體絕對路徑（已 realpath） */
    private string $contentRoot;

    public function __construct(string $contentRoot)
    {
        $resolved = realpath($contentRoot);
        $this->contentRoot = $resolved !== false ? $resolved : $contentRoot;
    }

    // ------------------------------------------------------------------
    // 路徑處理
    // ------------------------------------------------------------------

    /**
     * 把使用者輸入的相對路徑轉成安全的實體路徑
     *
     * 只允許 content-pages 底下的路徑，出現 .. 或跳出根目錄一律回傳 null。
     */
    private function resolve(string $relativePath): ?string
    {
        $relativePath = trim(str_replace('\\', '/', $relativePath), '/');

        if ($relativePath === '') {
            return $this->contentRoot;
        }

        foreach (explode('/', $relativePath) as $segment) {
            if ($segment === '' || $segment === '.' || $segment === '..') {
                return null;
            }
        }

        $candidate = $this->contentRoot . DIRECTORY_SEPARATOR
            . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
        $real = realpath($candidate);

        if ($real === false) {
            return null;
        }

        // 再次確認解析結果仍位於 content-pages 之內
        if ($real !== $this->contentRoot && !str_starts_with($real, $this->contentRoot . DIRECTORY_SEPARATOR)) {
            return null;
        }

        return $real;
    }

    /** 指定的相對路徑是否為資料夾 */
    public function isFolder(string $relativePath): bool
    {
        $resolved = $this->resolve($relativePath);

        return $resolved !== null && is_dir($resolved) && !$this->isImagesFolder($relativePath);
    }

    /** images 是圖片資產資料夾，不屬於文章內容，不列入瀏覽 */
    private function isImagesFolder(string $relativePath): bool
    {
        $relativePath = trim(str_replace('\\', '/', $relativePath), '/');

        return $relativePath === MC_IMAGES_DIRNAME
            || str_starts_with($relativePath, MC_IMAGES_DIRNAME . '/');
    }

    // ------------------------------------------------------------------
    // 列表
    // ------------------------------------------------------------------

    /**
     * 列出指定資料夾的子資料夾與文章
     *
     * 排序規則：子資料夾在前並依字母順序，文章在後並依檔名反向字母順序
     * （因為檔名以 YYYY-MM-DD 開頭，反向排序即為最新在上）。
     *
     * @return array{folders: array<int, array{name: string, label: string, route: string, url: string, pageCount: int}>, pages: ContentPage[]}
     */
    public function listFolder(string $relativePath = ''): array
    {
        $resolved = $this->resolve($relativePath);
        if ($resolved === null || !is_dir($resolved)) {
            return ['folders' => [], 'pages' => []];
        }

        $relativePath = trim(str_replace('\\', '/', $relativePath), '/');
        $folders = [];
        $pages = [];

        foreach (scandir($resolved) ?: [] as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $childRelative = ($relativePath === '' ? '' : $relativePath . '/') . $entry;
            $childAbsolute = $resolved . DIRECTORY_SEPARATOR . $entry;

            if (is_dir($childAbsolute)) {
                if ($this->isImagesFolder($childRelative)) {
                    continue;
                }

                $folders[] = [
                    'name' => $entry,
                    'label' => mc_folder_label($entry),
                    'route' => 'heritages/' . $childRelative,
                    'url' => mc_url('heritages/' . $childRelative),
                    'pageCount' => count($this->allPages($childRelative)),
                ];
                continue;
            }

            $page = ContentPage::fromFile($this->contentRoot, $childRelative);
            if ($page === null || !$page->isListable()) {
                continue;
            }

            $pages[] = $page;
        }

        // 子資料夾：字母順序
        usort($folders, static fn (array $a, array $b): int => strcasecmp($a['name'], $b['name']));

        // 文章：反向字母順序，讓最新的日期排在最上面
        usort($pages, static fn (ContentPage $a, ContentPage $b): int => strcmp($b->fileName(), $a->fileName()));

        return ['folders' => $folders, 'pages' => $pages];
    }

    /**
     * 遞迴取得所有「可列出」的文章，供標籤篩選與搜尋使用
     *
     * @return ContentPage[]
     */
    public function allPages(string $relativePath = ''): array
    {
        $resolved = $this->resolve($relativePath);
        if ($resolved === null || !is_dir($resolved)) {
            return [];
        }

        $relativePath = trim(str_replace('\\', '/', $relativePath), '/');
        $pages = [];

        foreach (scandir($resolved) ?: [] as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $childRelative = ($relativePath === '' ? '' : $relativePath . '/') . $entry;

            if (is_dir($resolved . DIRECTORY_SEPARATOR . $entry)) {
                if ($this->isImagesFolder($childRelative)) {
                    continue;
                }
                $pages = array_merge($pages, $this->allPages($childRelative));
                continue;
            }

            $page = ContentPage::fromFile($this->contentRoot, $childRelative);
            if ($page !== null && $page->isListable()) {
                $pages[] = $page;
            }
        }

        // 全站清單同樣採用反向字母順序（最新在上）
        usort($pages, static fn (ContentPage $a, ContentPage $b): int => strcmp($b->fileName(), $a->fileName()));

        return $pages;
    }

    // ------------------------------------------------------------------
    // 單篇文章
    // ------------------------------------------------------------------

    /**
     * 依網址的 slug 路徑找出文章
     *
     * 網址不帶副檔名，所以會依序嘗試 .html 與 .txt。
     * 例如 basilicas/2024-09-10-title → basilicas/2024-09-10-title.html
     */
    public function findPage(string $slugPath): ?ContentPage
    {
        $slugPath = trim(str_replace('\\', '/', $slugPath), '/');
        if ($slugPath === '' || $this->isImagesFolder($slugPath)) {
            return null;
        }

        foreach (MC_PAGE_EXTENSIONS as $extension) {
            $candidate = $slugPath . '.' . $extension;
            if ($this->resolve($candidate) === null) {
                continue;
            }

            $page = ContentPage::fromFile($this->contentRoot, $candidate);
            if ($page !== null) {
                return $page;
            }
        }

        return null;
    }

    // ------------------------------------------------------------------
    // 標籤與搜尋
    // ------------------------------------------------------------------

    /**
     * 取得帶有指定標籤的所有文章
     *
     * @return ContentPage[]
     */
    public function pagesByTag(string $tagSlug): array
    {
        return array_values(array_filter(
            $this->allPages(),
            static fn (ContentPage $page): bool => $page->hasTag($tagSlug)
        ));
    }

    /**
     * 全站標籤統計
     *
     * @return array<int, array{label: string, slug: string, count: int, url: string}>
     */
    public function allTags(): array
    {
        $tags = [];

        foreach ($this->allPages() as $page) {
            foreach ($page->tags() as $tag) {
                $slug = mc_slugify($tag);
                if ($slug === '') {
                    continue;
                }

                if (!isset($tags[$slug])) {
                    $tags[$slug] = ['label' => $tag, 'slug' => $slug, 'count' => 0, 'url' => mc_url('tags/' . $slug)];
                }
                $tags[$slug]['count']++;
            }
        }

        ksort($tags);

        return array_values($tags);
    }

    /**
     * 搜尋標題或內容
     *
     * 查詢字串可以用 "/" 分隔多個關鍵字，關鍵字之間是 OR 邏輯。
     *
     * @return ContentPage[]
     */
    public function search(string $query): array
    {
        $keywords = self::parseKeywords($query);
        if ($keywords === []) {
            return [];
        }

        return array_values(array_filter(
            $this->allPages(),
            static function (ContentPage $page) use ($keywords): bool {
                foreach ($keywords as $keyword) {
                    if ($page->matchesKeyword($keyword)) {
                        return true;   // OR 邏輯：命中任一關鍵字即納入結果
                    }
                }

                return false;
            }
        ));
    }

    /**
     * 把查詢字串切成關鍵字陣列
     *
     * @return string[]
     */
    public static function parseKeywords(string $query): array
    {
        $keywords = array_map('trim', explode('/', $query));

        return array_values(array_filter($keywords, static fn (string $k): bool => $k !== ''));
    }
}
