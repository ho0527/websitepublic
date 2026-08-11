<?php
/**
 * 模組 C - 全站設定
 *
 * 本專案完全以檔案為資料來源（content-pages 資料夾），不需要資料庫。
 * 這裡集中定義路徑、站台資訊與網址模式，其他程式一律引用這些常數，
 * 避免把實體路徑寫死在各個檔案裡。
 */

declare(strict_types=1);

/** app 資料夾（程式邏輯） */
define('MC_APP_DIR', __DIR__);

/** 模組根目錄（index.php 所在位置） */
define('MC_BASE_DIR', dirname(__DIR__));

/** 文章來源資料夾，結構必須維持編輯們原本的樣子，程式只讀不改 */
define('MC_CONTENT_DIR', MC_BASE_DIR . DIRECTORY_SEPARATOR . 'content-pages');

/** content-pages 底下唯一的圖片資料夾名稱（子資料夾內不會再有 images） */
define('MC_IMAGES_DIRNAME', 'images');

/** 站台名稱與描述，用於 <title>、社群分享 meta 標籤 */
define('MC_SITE_NAME', 'Lyon Heritage Sites');
define('MC_SITE_TAGLINE', '里昂遺產地點導覽');
define('MC_SITE_DESCRIPTION', 'A file based heritage site guide for Lyon, France. Articles are plain .html or .txt files with front-matter.');

/**
 * 網址模式
 *
 * true  代表伺服器已設定「單一入口重寫」（例如 nginx 的 try_files ... /index.php?route=$uri），
 *       此時連結會輸出規格書要求的乾淨路徑：/modulec/heritages/sub-folder/slug
 * false 代表使用查詢字串路由：/modulec/index.php?route=heritages/sub-folder/slug
 *
 * 兩種模式路由器都能解析（見 app/Router.php），差別只在「產生連結時」用哪一種。
 * 目前這台練習機的 nginx 沒有替本目錄設定重寫規則，因此預設使用 false，
 * 讓所有頁面在未改動伺服器設定的情況下都能正常瀏覽。README.md 附有啟用乾淨網址的設定片段。
 */
define('MC_CLEAN_URL', true);

/** 可被視為文章的副檔名 */
define('MC_PAGE_EXTENSIONS', ['html', 'txt']);

/** 可被視為圖片的副檔名（用於 .txt 內容中判斷「單獨一行的圖片路徑」） */
define('MC_IMAGE_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'svg']);

/** 列表摘要在沒有 front-matter summary 時，自動截取的字數上限 */
define('MC_SUMMARY_FALLBACK_LENGTH', 160);
