<?php
/**
 * TaskA 設定檔
 * 第 47 屆國際技能競賽第 2 階段國手選拔賽 - 模組 A（GraphQL 圖書管理系統）
 *
 * 說明：本機環境的 MySQL/MariaDB 帳號為 root、密碼空白，若環境不同請修改此處。
 */

return [
    // 資料庫連線設定
    'db' => [
        'host'     => '127.0.0.1',
        'port'     => 3306,
        'database' => 's53g2_library',
        'username' => 'root',
        'password' => '',
        'charset'  => 'utf8mb4',
    ],

    /*
     * 內建帳號使用的網域。
     * 試題中的帳號寫成 admin@webXX.com（XX 為崗位／國別代碼），
     * 但註冊範例又以 @localhost 出現，因此兩種網域都建立一組，方便評分時直接使用。
     * 第一組（localhost）的 id 會是 1（admin）與 2（user1），與試題範例的 id 相符。
     */
    'account_domains' => ['localhost', 'web01.com'],
];
