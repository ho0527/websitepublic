<?php
/**
 * 全域設定檔
 *
 * 這個檔案負責：
 *   1. 資料庫連線參數
 *   2. 管理員登入密碼（passphrase）
 *   3. 判斷目前的佈署模式，並算出對外網址的前綴（base path）
 *
 * 本模組支援兩種佈署方式（詳見 expert_readme.txt）：
 *   A. 乾淨網址模式：使用 PHP 內建網頁伺服器（php -S 127.0.0.1:8942 index.php），
 *      再由 nginx 的 /worldskill2024moduleb/ 反向代理出去，
 *      可以完整支援試題要求的 /products/[GTIN]、/01/[GTIN] 等路徑。
 *   B. 查詢字串模式：直接由 nginx 以靜態檔案方式提供 index.php，
 *      因為沒有 rewrite 規則，路由改用 index.php?route=/products/[GTIN] 傳遞。
 *
 * 兩種模式共用同一份程式碼，差別只在 urlFor() 產生連結的方式。
 */

declare(strict_types=1);

// ---------------------------------------------------------------------------
// 資料庫設定
// ---------------------------------------------------------------------------
const DB_HOST     = '127.0.0.1';
const DB_PORT     = 3306;
const DB_NAME     = 'worldskill2024_moduleb';
const DB_USER     = 'root';
const DB_PASSWORD = '';
const DB_CHARSET  = 'utf8mb4';

// ---------------------------------------------------------------------------
// 管理員設定
// ---------------------------------------------------------------------------
/** 試題指定的登入密碼（passphrase） */
const ADMIN_PASSPHRASE = 'admin';

/** session 名稱，避免與同一台主機上的其他專案互相覆蓋 */
const SESSION_NAME = 'WORLDSKILL2024_MODULEB_SESSION';

// ---------------------------------------------------------------------------
// 檔案上傳設定
// ---------------------------------------------------------------------------
/** 產品圖片實際存放的資料夾（絕對路徑） */
const UPLOAD_DIRECTORY = __DIR__ . '/media/uploads';

/** 允許上傳的圖片副檔名（小寫） */
const ALLOWED_IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];

/** 單張圖片大小上限（位元組），此處設為 8 MB */
const MAX_IMAGE_SIZE_BYTES = 8 * 1024 * 1024;

// ---------------------------------------------------------------------------
// 佈署模式與網址前綴
// ---------------------------------------------------------------------------
/**
 * 使用 PHP 內建伺服器並經 nginx 反向代理時，瀏覽器看到的路徑前綴。
 * 對應 nginx.conf 既有的 location /worldskill2024moduleb/ 設定。
 */
const REVERSE_PROXY_BASE = '/worldskill2024moduleb';

/**
 * 反向代理對外的來源（含通訊協定與埠號）。
 *
 * nginx 的 proxy_set_header Host $host 不會帶埠號，PHP 端無從得知對外埠號，
 * 因此在這裡明確指定，讓 API 分頁能產生正確的絕對網址。
 * 若對外網址改變，只需要修改這一行。
 */
const REVERSE_PROXY_ORIGIN = 'http://127.0.0.1:83';

/**
 * 計算對外網址前綴。
 *
 * @return string 例如 "/worldskill/2024/moduleb"（結尾不含斜線）
 */
function detectPublicBasePath(): string
{
    if (PHP_SAPI === 'cli-server') {
        // 由 nginx 反向代理進來時會帶 X-Forwarded-For，代表前面有一層路徑前綴；
        // 若是直接連 127.0.0.1:8942 測試則沒有前綴。
        return isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? REVERSE_PROXY_BASE : '';
    }

    // nginx 靜態模式：用本資料夾相對於網站根目錄的位置推算
    $documentRoot = isset($_SERVER['DOCUMENT_ROOT']) ? realpath($_SERVER['DOCUMENT_ROOT']) : false;
    $moduleRoot   = realpath(__DIR__);

    if ($documentRoot !== false && $moduleRoot !== false) {
        $documentRoot = str_replace('\\', '/', $documentRoot);
        $moduleRoot   = str_replace('\\', '/', $moduleRoot);

        if (strncmp($moduleRoot, $documentRoot, strlen($documentRoot)) === 0) {
            return rtrim(substr($moduleRoot, strlen($documentRoot)), '/');
        }
    }

    // 推算失敗時退回以 SCRIPT_NAME 推算（適用於直接呼叫 index.php 的情況）
    return rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
}

/** 是否使用「乾淨網址」模式（PATH 形式的路由） */
define('USE_CLEAN_URL', true);

/** 對外網址前綴，結尾不含斜線 */
define('PUBLIC_BASE_PATH', detectPublicBasePath());
