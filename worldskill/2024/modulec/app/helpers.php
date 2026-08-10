<?php
/**
 * 模組 C - 共用工具函式
 *
 * 這裡集中「輸出跳脫」與「網址組裝」兩件事：
 *  - 所有輸出到 HTML 的動態字串都要經過 mc_e()，避免 XSS。
 *  - 所有站內連結都要經過 mc_url() / mc_page_url()，讓網址模式可以一次切換。
 */

declare(strict_types=1);

/**
 * HTML 輸出跳脫（XSS 防護）
 */
function mc_e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * 取得本模組的基底網址，例如 /worldskill/2024/modulec/
 *
 * 由 SCRIPT_NAME 推導，因此不管模組被放在哪一層子目錄都能正確運作。
 */
function mc_base_url(): string
{
    static $baseUrl = null;
    if ($baseUrl !== null) {
        return $baseUrl;
    }

    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    $directory = str_replace('\\', '/', dirname($scriptName));
    $baseUrl = rtrim($directory, '/') . '/';

    return $baseUrl;
}

/**
 * 把路由片段組成完整網址
 *
 * @param string $route 例如 '' | 'heritages/museums' | 'tags/lyon'
 * @param array<string,string> $query 額外的查詢字串
 */
function mc_url(string $route = '', array $query = []): string
{
    $route = trim($route, '/');

    // 每個路徑片段各自編碼，斜線保持原樣才不會破壞路由結構
    $encodedRoute = implode('/', array_map('rawurlencode', $route === '' ? [] : explode('/', $route)));

    if (MC_CLEAN_URL) {
        $url = mc_base_url() . $encodedRoute;
        if ($query !== []) {
            $url .= '?' . http_build_query($query);
        }
        return $url;
    }

    // 查詢字串模式：一律走 index.php。
    // route 的斜線刻意不編碼，網址才能保留 heritages/folder/slug 的可讀結構。
    if ($route === '' && $query === []) {
        return mc_base_url();
    }

    $url = mc_base_url() . 'index.php?route=' . $encodedRoute;
    if ($query !== []) {
        $url .= '&' . http_build_query($query);
    }

    return $url;
}

/**
 * 靜態資源網址（CSS / JS）
 */
function mc_asset_url(string $relativePath): string
{
    return mc_base_url() . 'assets/' . ltrim($relativePath, '/');
}

/**
 * 圖片網址
 *
 * 文章中的圖片一律指向 content-pages/images/，即使文章本身位於子資料夾內，
 * 因此這裡只取檔名部分。若原始路徑已經是絕對網址則原樣保留。
 */
function mc_image_url(string $rawPath): string
{
    $rawPath = trim($rawPath);

    if ($rawPath === '') {
        return '';
    }

    // 已經是完整網址或站台絕對路徑，不做轉換
    if (preg_match('#^(https?:)?//#i', $rawPath) === 1 || str_starts_with($rawPath, '/')
        || str_starts_with($rawPath, 'data:')) {
        return $rawPath;
    }

    $fileName = basename(str_replace('\\', '/', $rawPath));

    return mc_base_url() . 'content-pages/' . MC_IMAGES_DIRNAME . '/' . rawurlencode($fileName);
}

/**
 * 取得目前請求的完整網址（社群分享 meta 的 og:url 需要）
 */
function mc_absolute_url(string $path): string
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

    if (preg_match('#^https?://#i', $path) === 1) {
        return $path;
    }

    return $scheme . '://' . $host . $path;
}

/**
 * 標籤 slug 化：轉小寫、空白與底線換成連字號
 *
 * 用來讓 /tags/tag-name-here 這種網址可以對應到 front-matter 內的「Tag Name Here」。
 */
function mc_slugify(string $text): string
{
    $text = trim(mb_strtolower($text, 'UTF-8'));
    $text = preg_replace('/[\s_]+/u', '-', $text) ?? $text;
    $text = preg_replace('/-{2,}/', '-', $text) ?? $text;

    return trim($text, '-');
}

/**
 * 把 slug 形式的檔名轉成標題大寫形式
 *
 * 例如 title-here → Title Here（連字號換成空白，每個字首字母大寫）。
 */
function mc_titleize(string $slug): string
{
    $words = preg_split('/-+/', trim($slug, '-')) ?: [];
    $words = array_filter($words, static fn (string $word): bool => $word !== '');

    $capitalised = array_map(
        static fn (string $word): string => mb_strtoupper(mb_substr($word, 0, 1, 'UTF-8'), 'UTF-8')
            . mb_substr($word, 1, null, 'UTF-8'),
        $words
    );

    return implode(' ', $capitalised);
}

/**
 * 把資料夾名稱轉成好讀的顯示名稱
 */
function mc_folder_label(string $folderName): string
{
    return mc_titleize($folderName);
}

/**
 * 由圖片檔名推導替代文字（alt）
 *
 * 去掉日期前綴與副檔名後轉成好讀的文字，例如
 * 2024-05-30-parc-de-la-tete-dor.jpeg → Parc De La Tete Dor
 */
function mc_image_alt_text(string $path): string
{
    $fileName = pathinfo(str_replace('\\', '/', $path), PATHINFO_FILENAME);
    $fileName = preg_replace('/^\d{4}-\d{2}-\d{2}-/', '', $fileName) ?? $fileName;

    return mc_titleize($fileName);
}

/**
 * 依副檔名判斷是否為圖片
 */
function mc_is_image_file(string $path): bool
{
    $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

    return in_array($extension, MC_IMAGE_EXTENSIONS, true);
}

/**
 * 截斷純文字並補上刪節號，用於沒有 summary 時的備援摘要
 */
function mc_excerpt(string $text, int $length = MC_SUMMARY_FALLBACK_LENGTH): string
{
    $text = trim(preg_replace('/\s+/u', ' ', $text) ?? $text);

    if ($text === '' || mb_strlen($text, 'UTF-8') <= $length) {
        return $text;
    }

    return rtrim(mb_substr($text, 0, $length, 'UTF-8')) . '…';
}
