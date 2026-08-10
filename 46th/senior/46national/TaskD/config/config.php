<?php
/**
 * 應用程式設定
 */

return [
    // 資料庫連線設定
    'database' => [
        'host'     => '127.0.0.1',
        'port'     => 3306,
        'name'     => '46_national_moduled',
        'user'     => 'root',
        'password' => '',
        'charset'  => 'utf8mb4',
    ],

    // 時區，影響所有時間計算與顯示
    'timezone' => 'Asia/Taipei',

    // 簡訊檔案輸出目錄（相對於網站根目錄）
    'sms_directory' => 'SMS',

    // 問答驗證碼設定
    'captcha' => [
        // 題目圖片所在目錄（相對於網站根目錄）
        'image_directory' => 'assets/captcha',
        // 點擊後產生的標記矩形尺寸（以游標為中心）
        'marker_width'    => 120,
        'marker_height'   => 100,
        // 標記矩形的框線寬度，試題要求至少 5px
        'marker_border'   => 5,
    ],

    // 分頁筆數
    'pagination' => [
        // 前台訂票查詢每頁 3 筆
        'front_bookings' => 3,
        // 後台訂票紀錄每頁 5 筆
        'admin_bookings' => 5,
    ],

    // 訂票限制
    'booking' => [
        'min_tickets' => 1,
        'max_tickets' => 1000,
        // 訂票編號長度（12 碼英數字，區分大小寫）
        'code_length' => 12,
    ],

    // 開放資料統計的時間單位（分鐘）
    'statistics_interval_minutes' => 30,
];
