<?php
/**
 * 應用程式設定檔
 * WorldSkills 2015 TP17 Server Side B（Module H）Restaurant Service 訂位系統
 */

return [
    // 資料庫連線設定
    'db' => [
        'host'     => '127.0.0.1',
        'port'     => 3306,
        'name'     => 'worldskill2015_moduleh',
        'user'     => 'root',
        'password' => '',
        'charset'  => 'utf8mb4',
    ],

    // 應用程式基本設定
    'app' => [
        // 網站根目錄下的模組路徑（用於產生連結）
        'base_path'   => '/worldskill/2015/moduleh',
        // 訂位編號前綴（例如 201500021）
        'booking_prefix' => '2015',
        // 是否輸出乾淨網址（需先套用 README.md 中的 nginx rewrite 設定）
        // 預設 false：使用 index.php/booking/individual 這種 PATH_INFO 等效寫法
        'clean_urls'  => false,
        // Send emails 產生的文字檔存放目錄
        'email_dir'   => __DIR__ . '/../emails',
    ],
];
