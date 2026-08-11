<?php
/**
 * 資料庫與系統參數設定
 * 第51屆全國技能競賽 網頁技術 模組D - 房屋交易平台
 */

return [
    // 資料庫連線設定
    'db' => [
        'host'     => '127.0.0.1',
        'port'     => 3306,
        'name'     => 'worldskill51_moduled',
        'user'     => 'root',
        'password' => '',
        'charset'  => 'utf8mb4',
    ],

    // 圖片上傳目錄（相對於模組根目錄）
    'upload_dir' => 'uploads',

    // 允許上傳的圖片副檔名
    'allow_image_ext' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],

    // 每頁資料筆數
    'per_page' => 10,

    // 精選房屋核准後的展示天數
    'ad_days' => 7,
];
