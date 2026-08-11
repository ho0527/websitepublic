<?php
/**
 * Plugin Name: Plugin Works
 * Plugin URI:  https://worldskills.org/
 * Description: 啟用後會在後台左側選單新增一個項目，點進去後顯示標題「Plugin works!」。
 * Version:     1.0.0
 * Author:      WSC2019 TP17 Speed Test - D02
 * Text Domain: plugin-works
 */

/* 禁止直接以網址存取本檔案 */
if (!defined('ABSPATH')) {
	exit;
}

/**
 * 在後台選單註冊「Plugin works」項目
 */
function plugin_works_register_menu()
{
	add_menu_page(
		'Plugin works!',                 // 瀏覽器分頁標題
		'Plugin works',                  // 選單顯示文字
		'manage_options',                // 需要的權限
		'plugin-works',                  // 選單代稱（網址 slug）
		'plugin_works_render_page',      // 畫面輸出的回呼函式
		'dashicons-yes-alt',             // 選單圖示
		61                               // 選單排列位置
	);
}
add_action('admin_menu', 'plugin_works_register_menu');

/**
 * 選單頁面的內容：顯示標題「Plugin works!」
 */
function plugin_works_render_page()
{
	echo '<div class="wrap">';
	echo '<h1>Plugin works!</h1>';
	echo '<p>這個頁面由 Plugin Works 外掛所建立，代表外掛已成功啟用並正常運作。</p>';
	echo '</div>';
}
