<?php
/**
 * 全站設定檔
 *
 * WorldSkills 2019 Skill 17 模組 B（CMS and Layout）— Kazan MuseumTour
 * 這裡只放「環境相依」的設定，其餘可由後台維護的設定都存在資料庫 settings 資料表。
 */

declare(strict_types=1);

return [
    // 資料庫連線資訊（PDO）
    'db' => [
        'host'     => '127.0.0.1',
        'port'     => 3306,
        'name'     => 'worldskill2019_taskb',
        'user'     => 'root',
        'password' => '',
        'charset'  => 'utf8mb4',
    ],

    /*
     * 網站根路徑（結尾一定要有斜線）。
     * 若整站搬到網域根目錄，改成 '/' 即可。
     */
    'base_path' => '/worldskill/2019/TaskB/',

    /*
     * 是否啟用「乾淨網址」。
     * false：網址形式為 <base>index.php/museum-of-national-culture/（不需要改 nginx 設定）
     * true ：網址形式為 <base>museum-of-national-culture/（需在 nginx 加上 try_files，見 README）
     */
    'clean_urls' => false,

    // 後台入口路徑（規格要求 <host>/admin/）
    'admin_path' => 'admin',

    // Session 名稱，避免與同一台主機上的其他練習專案互相覆蓋
    'session_name' => 'wsc2019_taskb_sid',
];
