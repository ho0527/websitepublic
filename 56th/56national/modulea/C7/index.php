<?php
/**
 * C7: 階層式選單生成工具（等級 1）
 * 使用者以逐行方式輸入分類，行內用 ">" 表示階層，
 * 送出後解析成巢狀 <ul> / <li> 選單並顯示預覽。
 */

declare(strict_types=1);

$rawInput = (string) ($_POST['categories'] ?? '');

/**
 * 把每行以分隔符號拆開的分類，組成巢狀陣列
 * @param string $text 使用者輸入
 * @return array 巢狀結構：['名稱' => [子項...], ...]
 */
function buildTree(string $text): array
{
    $tree = [];
    $lines = preg_split('/\R/', $text) ?: [];

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }
        // 以 > 拆出各層名稱
        $parts = array_values(array_filter(array_map('trim', explode('>', $line)), static function (string $part): bool {
            return $part !== '';
        }));
        if ($parts === []) {
            continue;
        }

        // 沿著路徑往下建立節點
        $cursor = &$tree;
        foreach ($parts as $part) {
            if (!isset($cursor[$part])) {
                $cursor[$part] = [];
            }
            $cursor = &$cursor[$part];
        }
        unset($cursor);
    }

    return $tree;
}

/**
 * 將巢狀陣列輸出為 <ul>/<li>
 */
function renderTree(array $tree, int $depth = 0): string
{
    if ($tree === []) {
        return '';
    }
    $indent = str_repeat("\t", $depth);
    $html = $indent . "<ul>\n";
    foreach ($tree as $name => $children) {
        $html .= $indent . "\t<li>" . htmlspecialchars((string) $name, ENT_QUOTES, 'UTF-8');
        if ($children !== []) {
            $html .= "\n" . renderTree($children, $depth + 2) . $indent . "\t";
        }
        $html .= "</li>\n";
    }
    $html .= $indent . "</ul>\n";
    return $html;
}

$defaultInput = "電子產品 > 電腦 > 筆記型電腦\n電子產品 > 電腦 > 桌上型電腦\n電子產品 > 手機\n服飾 > 男裝 > 上衣\n服飾 > 女裝\n食品";
$tree = $rawInput === '' ? [] : buildTree($rawInput);
$menuHtml = renderTree($tree);
?>
<!DOCTYPE html>
<html lang="zh-Hant">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>C7 階層式選單生成工具</title>
	<style>
		body {
			margin: 0px;
			padding: 34px 20px;
			background: #f6f8fa;
			font-family: "Microsoft JhengHei", Arial, Helvetica, sans-serif;
			color: #1f2328;
			display: flex;
			flex-direction: column;
			align-items: center;
			gap: 16px;
		}

		h1 {
			font-size: 20px;
			margin: 0px;
		}

		.hint {
			color: #59636e;
			font-size: 14px;
			margin: 0px;
		}

		form {
			width: min(90vw, 620px);
			display: flex;
			flex-direction: column;
			gap: 12px;
		}

		textarea {
			width: 100%;
			height: 160px;
			padding: 12px;
			font-size: 15px;
			font-family: inherit;
			line-height: 1.7;
			border: 1px solid #d0d7de;
			border-radius: 8px;
			resize: vertical;
			box-sizing: border-box;
		}

		button {
			align-self: flex-start;
			background: #1f6feb;
			color: #ffffff;
			border: none;
			border-radius: 6px;
			padding: 10px 26px;
			font-size: 15px;
			font-family: inherit;
			cursor: pointer;
		}

		button:hover {
			background: #3b82f6;
		}

		.panels {
			width: min(90vw, 620px);
			display: flex;
			flex-direction: column;
			gap: 14px;
		}

		.panel {
			background: #ffffff;
			border: 1px solid #d0d7de;
			border-radius: 10px;
			padding: 16px 20px;
		}

		.panel h2 {
			font-size: 15px;
			margin: 0px 0px 10px 0px;
			color: #59636e;
			font-weight: normal;
		}

		/* 選單預覽 */
		.preview ul {
			margin: 0px;
			padding-left: 22px;
		}

		.preview li {
			line-height: 2;
		}

		/* 次階層以較淡的顏色呈現，看得出是子選單 */
		.preview ul ul li {
			color: #445;
			font-size: 15px;
		}

		pre {
			background: #0f1216;
			color: #e6edf3;
			font-size: 12.5px;
			line-height: 1.6;
			padding: 14px;
			border-radius: 8px;
			overflow-x: auto;
			margin: 0px;
		}
	</style>
</head>

<body>
	<h1>階層式選單生成工具</h1>
	<p class="hint">每行一筆分類，使用「&gt;」表示階層，例如：電子產品 &gt; 電腦 &gt; 筆記型電腦</p>

	<form method="post">
		<textarea name="categories"><?= htmlspecialchars($rawInput !== '' ? $rawInput : $defaultInput, ENT_QUOTES, 'UTF-8') ?></textarea>
		<button type="submit">生成選單</button>
	</form>

	<?php if ($menuHtml !== ''): ?>
		<div class="panels">
			<div class="panel preview">
				<h2>選單預覽</h2>
				<?= $menuHtml ?>
			</div>
			<div class="panel">
				<h2>產生的 HTML</h2>
				<pre><?= htmlspecialchars($menuHtml, ENT_QUOTES, 'UTF-8') ?></pre>
			</div>
		</div>
	<?php endif; ?>
</body>

</html>
