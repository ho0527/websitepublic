<?php
/**
 * D08 File manager
 * ---------------------------------------------------------------------------
 * 題目：製作一個檔案管理員，可以瀏覽檔案系統、刪除檔案與資料夾、編輯檔案並存檔。
 *
 * 為了避免誤刪整個網站，管理範圍限制在本資料夾下的 workspace/ 內（見 $ROOT）。
 * 只要修改 $ROOT 就能改成管理其他目錄。
 */

/* 管理的根目錄（所有操作都不允許超出此範圍） */
$ROOT = realpath(__DIR__ . '/workspace');

if ($ROOT === false) {
	mkdir(__DIR__ . '/workspace', 0777, true);
	$ROOT = realpath(__DIR__ . '/workspace');
}

/* 可直接編輯的純文字副檔名 */
$EDITABLE = ['txt', 'md', 'html', 'htm', 'css', 'js', 'json', 'php', 'xml', 'csv', 'ini', 'log', 'sql', 'yml', 'yaml'];

/* ---------------------------------------------------------------------------
   路徑處理：把使用者傳來的相對路徑轉成絕對路徑，並確認仍在 $ROOT 之內
   --------------------------------------------------------------------------- */
function resolve_path($relative)
{
	global $ROOT;

	$relative = str_replace('\\', '/', (string) $relative);
	$target = $ROOT . ($relative === '' ? '' : '/' . $relative);

	/* 逐段檢查，避免 ../ 跳出根目錄 */
	$safe = [];
	foreach (explode('/', $relative) as $segment) {
		if ($segment === '' || $segment === '.') {
			continue;
		}
		if ($segment === '..') {
			array_pop($safe);
			continue;
		}
		$safe[] = $segment;
	}

	$relative = implode('/', $safe);
	$target = $ROOT . ($relative === '' ? '' : DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative));

	return ['relative' => $relative, 'absolute' => $target];
}

/** 遞迴刪除資料夾 */
function delete_recursive($path)
{
	if (is_dir($path)) {
		foreach (array_diff(scandir($path), ['.', '..']) as $child) {
			delete_recursive($path . DIRECTORY_SEPARATOR . $child);
		}
		return rmdir($path);
	}
	return is_file($path) ? unlink($path) : false;
}

/** 把檔案大小轉成易讀格式 */
function format_size($bytes)
{
	$units = ['B', 'KB', 'MB', 'GB'];
	$index = 0;
	while ($bytes >= 1024 && $index < count($units) - 1) {
		$bytes /= 1024;
		$index += 1;
	}
	return round($bytes, $index === 0 ? 0 : 1) . ' ' . $units[$index];
}

/** 取得副檔名（小寫） */
function extension_of($name)
{
	return strtolower(pathinfo($name, PATHINFO_EXTENSION));
}

/* ---------------------------------------------------------------------------
   接收動作
   --------------------------------------------------------------------------- */
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'list';
$pathInfo = resolve_path(isset($_REQUEST['path']) ? $_REQUEST['path'] : '');
$message = null;      // 操作結果訊息
$messageType = 'ok';

/* 目前所在的資料夾（編輯檔案時為該檔案所屬資料夾） */
$currentDir = is_dir($pathInfo['absolute']) ? $pathInfo['relative'] : dirname($pathInfo['relative']);
$currentDir = ($currentDir === '.' || $currentDir === DIRECTORY_SEPARATOR) ? '' : str_replace('\\', '/', $currentDir);

if ($action === 'delete' && $pathInfo['relative'] !== '') {
	/* 刪除檔案或資料夾 */
	if (file_exists($pathInfo['absolute']) && delete_recursive($pathInfo['absolute'])) {
		$message = '已刪除：' . $pathInfo['relative'];
	} else {
		$message = '刪除失敗：' . $pathInfo['relative'];
		$messageType = 'err';
	}
	$currentDir = str_replace('\\', '/', dirname($pathInfo['relative']));
	$currentDir = ($currentDir === '.' ) ? '' : $currentDir;
	$action = 'list';

} elseif ($action === 'save' && $_SERVER['REQUEST_METHOD'] === 'POST') {
	/* 儲存編輯後的檔案內容 */
	$content = isset($_POST['content']) ? $_POST['content'] : '';
	$content = str_replace("\r\n", "\n", $content);

	if (is_file($pathInfo['absolute']) && file_put_contents($pathInfo['absolute'], $content) !== false) {
		$message = '已儲存：' . $pathInfo['relative'];
	} else {
		$message = '儲存失敗：' . $pathInfo['relative'];
		$messageType = 'err';
	}
	$action = 'edit';

} elseif ($action === 'mkdir' && $_SERVER['REQUEST_METHOD'] === 'POST') {
	/* 新增資料夾 */
	$name = trim((string) $_POST['name']);
	$target = resolve_path(($pathInfo['relative'] === '' ? '' : $pathInfo['relative'] . '/') . $name);

	if ($name === '' || preg_match('/[\\\\\/:*?"<>|]/', $name)) {
		$message = '資料夾名稱不合法';
		$messageType = 'err';
	} elseif (file_exists($target['absolute'])) {
		$message = '同名項目已存在';
		$messageType = 'err';
	} elseif (mkdir($target['absolute'])) {
		$message = '已新增資料夾：' . $target['relative'];
	} else {
		$message = '新增資料夾失敗';
		$messageType = 'err';
	}
	$action = 'list';

} elseif ($action === 'touch' && $_SERVER['REQUEST_METHOD'] === 'POST') {
	/* 新增空白檔案 */
	$name = trim((string) $_POST['name']);
	$target = resolve_path(($pathInfo['relative'] === '' ? '' : $pathInfo['relative'] . '/') . $name);

	if ($name === '' || preg_match('/[\\\\\/:*?"<>|]/', $name)) {
		$message = '檔案名稱不合法';
		$messageType = 'err';
	} elseif (file_exists($target['absolute'])) {
		$message = '同名項目已存在';
		$messageType = 'err';
	} elseif (file_put_contents($target['absolute'], '') !== false) {
		$message = '已新增檔案：' . $target['relative'];
	} else {
		$message = '新增檔案失敗';
		$messageType = 'err';
	}
	$action = 'list';
}

/* ---------------------------------------------------------------------------
   準備畫面資料
   --------------------------------------------------------------------------- */
$editing = null;
if ($action === 'edit' && is_file($pathInfo['absolute'])) {
	$editing = [
		'relative' => $pathInfo['relative'],
		'content'  => file_get_contents($pathInfo['absolute']),
		'editable' => in_array(extension_of($pathInfo['absolute']), $EDITABLE, true),
	];
	$currentDir = str_replace('\\', '/', dirname($pathInfo['relative']));
	$currentDir = ($currentDir === '.') ? '' : $currentDir;
}

/* 列出目前資料夾的內容，資料夾排在前面 */
$listDir = resolve_path($currentDir);
$entries = [];
if (is_dir($listDir['absolute'])) {
	foreach (array_diff(scandir($listDir['absolute']), ['.', '..']) as $name) {
		$full = $listDir['absolute'] . DIRECTORY_SEPARATOR . $name;
		$entries[] = [
			'name'     => $name,
			'relative' => ($listDir['relative'] === '' ? '' : $listDir['relative'] . '/') . $name,
			'isDir'    => is_dir($full),
			'size'     => is_dir($full) ? '-' : format_size(filesize($full)),
			'time'     => date('Y-m-d H:i', filemtime($full)),
			'editable' => !is_dir($full) && in_array(extension_of($name), $EDITABLE, true),
		];
	}
	usort($entries, function ($a, $b) {
		if ($a['isDir'] !== $b['isDir']) {
			return $a['isDir'] ? -1 : 1;
		}
		return strcasecmp($a['name'], $b['name']);
	});
}

/* 麵包屑 */
$crumbs = [['name' => 'workspace', 'path' => '']];
$walk = '';
foreach (array_filter(explode('/', $listDir['relative'])) as $segment) {
	$walk = ($walk === '' ? '' : $walk . '/') . $segment;
	$crumbs[] = ['name' => $segment, 'path' => $walk];
}

/** 簡短的 HTML 跳脫 */
function e($text)
{
	return htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="zh-Hant">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>D08 - File manager</title>
	<link rel="stylesheet" href="filemanager.css">
</head>

<body>
	<div class="app">
		<header class="app__head">
			<h1>File manager</h1>
			<p>瀏覽檔案系統、編輯檔案並存檔、刪除檔案與資料夾。管理範圍：<code>D08/workspace</code></p>
		</header>

		<?php if ($message !== null): ?>
			<p class="flash flash--<?php echo e($messageType); ?>"><?php echo e($message); ?></p>
		<?php endif; ?>

		<!-- 路徑麵包屑 -->
		<nav class="crumbs">
			<?php foreach ($crumbs as $index => $crumb): ?>
				<?php if ($index > 0): ?><span class="crumbs__sep">/</span><?php endif; ?>
				<a href="?path=<?php echo e(rawurlencode($crumb['path'])); ?>"><?php echo e($crumb['name']); ?></a>
			<?php endforeach; ?>
		</nav>

		<div class="panels">
			<!-- 左側：檔案清單 -->
			<section class="panel panel--list">
				<div class="panel__head">
					<span>目前資料夾（<?php echo count($entries); ?> 個項目）</span>
					<?php if ($listDir['relative'] !== ''): ?>
						<a class="btn btn--sm"
							href="?path=<?php echo e(rawurlencode(dirname($listDir['relative']) === '.' ? '' : dirname($listDir['relative']))); ?>">上一層</a>
					<?php endif; ?>
				</div>

				<table class="table">
					<thead>
						<tr>
							<th>名稱</th>
							<th>大小</th>
							<th>修改時間</th>
							<th class="table__ops">操作</th>
						</tr>
					</thead>
					<tbody>
						<?php if (count($entries) === 0): ?>
							<tr>
								<td colspan="4" class="table__empty">（這個資料夾是空的）</td>
							</tr>
						<?php endif; ?>

						<?php foreach ($entries as $entry): ?>
							<tr<?php echo ($editing !== null && $editing['relative'] === $entry['relative']) ? ' class="is-open"' : ''; ?>>
								<td>
									<?php if ($entry['isDir']): ?>
										<a class="name name--dir" href="?path=<?php echo e(rawurlencode($entry['relative'])); ?>">
											<span class="ico ico--dir"></span><?php echo e($entry['name']); ?>
										</a>
									<?php elseif ($entry['editable']): ?>
										<a class="name" href="?action=edit&amp;path=<?php echo e(rawurlencode($entry['relative'])); ?>">
											<span class="ico ico--file"></span><?php echo e($entry['name']); ?>
										</a>
									<?php else: ?>
										<span class="name name--plain">
											<span class="ico ico--bin"></span><?php echo e($entry['name']); ?>
										</span>
									<?php endif; ?>
								</td>
								<td class="num"><?php echo e($entry['size']); ?></td>
								<td class="num"><?php echo e($entry['time']); ?></td>
								<td class="table__ops">
									<?php if ($entry['editable']): ?>
										<a class="btn btn--sm"
											href="?action=edit&amp;path=<?php echo e(rawurlencode($entry['relative'])); ?>">編輯</a>
									<?php endif; ?>
									<a class="btn btn--sm btn--danger"
										href="?action=delete&amp;path=<?php echo e(rawurlencode($entry['relative'])); ?>"
										onclick="return confirm('確定要刪除「<?php echo e($entry['name']); ?>」嗎？<?php echo $entry['isDir'] ? '資料夾內的所有內容都會一併刪除。' : ''; ?>');">刪除</a>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>

				<!-- 新增資料夾 / 檔案 -->
				<div class="creators">
					<form method="post" action="?action=mkdir&amp;path=<?php echo e(rawurlencode($listDir['relative'])); ?>">
						<input type="text" name="name" placeholder="新資料夾名稱" required>
						<button class="btn" type="submit">新增資料夾</button>
					</form>
					<form method="post" action="?action=touch&amp;path=<?php echo e(rawurlencode($listDir['relative'])); ?>">
						<input type="text" name="name" placeholder="新檔案名稱（例：note.txt）" required>
						<button class="btn" type="submit">新增檔案</button>
					</form>
				</div>
			</section>

			<!-- 右側：編輯器 -->
			<section class="panel panel--editor">
				<?php if ($editing === null): ?>
					<div class="panel__head"><span>檔案編輯器</span></div>
					<p class="hint">從左側清單點選一個文字檔即可開始編輯。</p>
				<?php elseif (!$editing['editable']): ?>
					<div class="panel__head"><span><?php echo e($editing['relative']); ?></span></div>
					<p class="hint">這個副檔名不是純文字格式，無法直接編輯。</p>
				<?php else: ?>
					<form method="post" action="?action=save&amp;path=<?php echo e(rawurlencode($editing['relative'])); ?>">
						<div class="panel__head">
							<span>編輯中：<?php echo e($editing['relative']); ?></span>
							<span class="panel__ops">
								<button class="btn btn--primary" type="submit">儲存</button>
								<a class="btn" href="?path=<?php echo e(rawurlencode($listDir['relative'])); ?>">關閉</a>
							</span>
						</div>
						<textarea class="editor" name="content" spellcheck="false"><?php echo e($editing['content']); ?></textarea>
					</form>
				<?php endif; ?>
			</section>
		</div>
	</div>
</body>

</html>
