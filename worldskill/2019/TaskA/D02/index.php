<?php
/**
 * D02 Plugin works
 * ---------------------------------------------------------------------------
 * 題目：製作一個外掛，啟用後在後台新增一個選單項目，點進去顯示標題「Plugin works!」。
 *
 * 成品外掛檔：wordpress/wp-content/plugins/plugin-works/plugin-works.php
 *
 * 本機的 PHP 8.3 未載入 mysqli，而素材附的 WordPress 為 4.9.9（需要 mysqli 且不相容 PHP 8），
 * 因此無法直接啟動該份 WordPress。為了讓外掛能被實際執行與驗收，
 * 這支檔案提供一組最小的 WordPress 外掛 API（add_action / do_action / add_menu_page），
 * 再原封不動地載入上面那支外掛檔，用它真正註冊的選單與回呼函式渲染出後台畫面。
 */

define('ABSPATH', __DIR__ . '/wordpress/');

$GLOBALS['wsc_actions'] = [];   // 掛勾清單
$GLOBALS['wsc_menus']   = [];   // 外掛註冊的後台選單

/** 註冊掛勾（對應 WordPress 的 add_action） */
function add_action($hook, $callback, $priority = 10)
{
	$GLOBALS['wsc_actions'][$hook][$priority][] = $callback;
}

/** 觸發掛勾（對應 WordPress 的 do_action） */
function do_action($hook)
{
	if (empty($GLOBALS['wsc_actions'][$hook])) {
		return;
	}
	ksort($GLOBALS['wsc_actions'][$hook]);
	foreach ($GLOBALS['wsc_actions'][$hook] as $callbacks) {
		foreach ($callbacks as $callback) {
			call_user_func($callback);
		}
	}
}

/** 新增後台主選單（對應 WordPress 的 add_menu_page） */
function add_menu_page($page_title, $menu_title, $capability, $menu_slug, $callback = '', $icon = '', $position = null)
{
	$GLOBALS['wsc_menus'][] = [
		'page_title' => $page_title,
		'menu_title' => $menu_title,
		'menu_slug'  => $menu_slug,
		'callback'   => $callback,
		'position'   => $position,
	];
	return $menu_slug;
}

/* 載入外掛（模擬「啟用外掛」）後觸發 admin_menu，外掛便會註冊自己的選單 */
require_once __DIR__ . '/wordpress/wp-content/plugins/plugin-works/plugin-works.php';
do_action('admin_menu');

/* 目前要顯示的頁面 */
$current = isset($_GET['page']) ? (string) $_GET['page'] : '';
$plugin_source = file_get_contents(__DIR__ . '/wordpress/wp-content/plugins/plugin-works/plugin-works.php');

/* WordPress 內建選單（僅作為畫面示意） */
$core_menus = ['控制台', '文章', '媒體', '頁面', '留言', '外觀', '外掛', '使用者', '工具', '設定'];
?>
<!DOCTYPE html>
<html lang="zh-Hant">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>D02 - Plugin works</title>
	<link rel="stylesheet" href="admin.css">
</head>

<body>
	<div class="adminbar">WordPress 後台（模擬畫面）</div>

	<div class="layout">
		<!-- 後台左側選單：內建項目 + 外掛註冊的項目 -->
		<nav class="menu">
			<?php foreach ($core_menus as $name): ?>
				<span class="menu__item is-core"><?php echo htmlspecialchars($name); ?></span>
			<?php endforeach; ?>

			<?php foreach ($GLOBALS['wsc_menus'] as $menu): ?>
				<a class="menu__item<?php echo $current === $menu['menu_slug'] ? ' is-active' : ''; ?>"
					href="?page=<?php echo urlencode($menu['menu_slug']); ?>">
					<?php echo htmlspecialchars($menu['menu_title']); ?>
				</a>
			<?php endforeach; ?>
		</nav>

		<main class="content">
			<?php
			/* 找出目前選單並執行它的回呼函式，輸出外掛自己的畫面 */
			$matched = null;
			foreach ($GLOBALS['wsc_menus'] as $menu) {
				if ($menu['menu_slug'] === $current) {
					$matched = $menu;
					break;
				}
			}

			if ($matched !== null) {
				call_user_func($matched['callback']);
			} else {
			?>
				<div class="wrap">
					<h1>D02：Plugin works</h1>
					<p>外掛已載入並註冊選單，請點擊左側選單的
						<a href="?page=plugin-works"><strong>Plugin works</strong></a>
						查看外掛頁面（標題為「Plugin works!」）。
					</p>

					<h2>外掛檔案位置</h2>
					<p class="path">wordpress/wp-content/plugins/plugin-works/plugin-works.php</p>

					<h2>外掛原始碼</h2>
					<pre class="code"><?php echo htmlspecialchars($plugin_source); ?></pre>

					<h2>安裝方式（在可正常執行的 WordPress 上）</h2>
					<ol>
						<li>把 <code>plugin-works</code> 資料夾放進 <code>wp-content/plugins/</code>。</li>
						<li>進入後台「外掛」頁面，按下「啟用」。</li>
						<li>左側選單會出現「Plugin works」，點進去即顯示標題「Plugin works!」。</li>
					</ol>
				</div>
			<?php } ?>
		</main>
	</div>
</body>

</html>
