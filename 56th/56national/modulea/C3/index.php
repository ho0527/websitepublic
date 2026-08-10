<?php
/**
 * C3: 文件內容搜索（等級 2）
 * 由表單輸入關鍵字，掃描 source/ 目錄中所有 .txt 檔，
 * 以不區分大小寫的方式逐行比對，結果依檔案分組並標註行號。
 */

declare(strict_types=1);

$keyword = trim((string) ($_GET['keyword'] ?? ''));
$results = [];        // 檔名 => [['line' => 行號, 'text' => 內容], ...]
$scannedFiles = 0;
$message = '';

if ($keyword !== '') {
    $files = glob(__DIR__ . '/source/*.txt') ?: [];
    $scannedFiles = count($files);

    if ($scannedFiles === 0) {
        $message = 'source/ 資料夾中沒有任何 .txt 檔案。';
    } else {
        foreach ($files as $file) {
            $lines = file($file, FILE_IGNORE_NEW_LINES);
            if ($lines === false) {
                continue;
            }
            foreach ($lines as $index => $line) {
                // stripos：不區分大小寫的比對
                if (stripos($line, $keyword) !== false) {
                    $results[basename($file)][] = [
                        'line' => $index + 1,
                        'text' => $line,
                    ];
                }
            }
        }
        if ($results === []) {
            $message = '找不到符合「' . $keyword . '」的內容。';
        }
    }
}

/**
 * 將命中的關鍵字包上標記（同樣不分大小寫）
 */
function highlight(string $text, string $keyword): string
{
    $escaped = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    if ($keyword === '') {
        return $escaped;
    }
    $pattern = '/' . preg_quote(htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8'), '/') . '/i';
    return preg_replace($pattern, '<mark>$0</mark>', $escaped) ?? $escaped;
}
?>
<!DOCTYPE html>
<html lang="zh-Hant">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>C3 文件內容搜索</title>
	<style>
		body {
			margin: 0px;
			padding: 36px 20px;
			background: #f6f8fa;
			font-family: "Microsoft JhengHei", Arial, Helvetica, sans-serif;
			color: #1f2328;
			display: flex;
			flex-direction: column;
			align-items: center;
			gap: 18px;
		}

		h1 {
			font-size: 20px;
			margin: 0px;
		}

		form {
			display: flex;
			gap: 10px;
		}

		input[type="text"] {
			width: 320px;
			padding: 10px 12px;
			font-size: 15px;
			font-family: inherit;
			border: 1px solid #d0d7de;
			border-radius: 6px;
			outline: none;
		}

		input[type="text"]:focus {
			border-color: #1f6feb;
		}

		button {
			background: #1f6feb;
			color: #ffffff;
			border: none;
			border-radius: 6px;
			padding: 10px 24px;
			font-size: 15px;
			font-family: inherit;
			cursor: pointer;
		}

		button:hover {
			background: #3b82f6;
		}

		.results {
			width: min(90vw, 780px);
			display: flex;
			flex-direction: column;
			gap: 14px;
		}

		.file {
			background: #ffffff;
			border: 1px solid #d0d7de;
			border-radius: 10px;
			overflow: hidden;
		}

		.file h2 {
			font-size: 15px;
			margin: 0px;
			padding: 10px 14px;
			background: #f6f8fa;
			border-bottom: 1px solid #d0d7de;
		}

		.file h2 span {
			color: #59636e;
			font-weight: normal;
			font-size: 13px;
		}

		.hit {
			display: flex;
			gap: 12px;
			padding: 8px 14px;
			border-bottom: 1px solid #f0f2f5;
			font-size: 14px;
		}

		.hit:last-child {
			border-bottom: none;
		}

		.no {
			color: #8b949e;
			min-width: 52px;
			font-variant-numeric: tabular-nums;
		}

		mark {
			background: #fff3cf;
			padding: 0px 2px;
			border-radius: 2px;
		}

		.message {
			color: #59636e;
			font-size: 15px;
		}
	</style>
</head>

<body>
	<h1>文件內容搜索</h1>

	<form method="get">
		<input type="text" name="keyword" placeholder="輸入搜尋關鍵字（不分大小寫）"
			value="<?= htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8') ?>" required>
		<button type="submit">搜尋</button>
	</form>

	<?php if ($message !== ''): ?>
		<p class="message"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
	<?php endif; ?>

	<?php if ($results !== []): ?>
		<p class="message">
			共掃描 <?= $scannedFiles ?> 個檔案，於 <?= count($results) ?> 個檔案中找到結果。
		</p>
		<div class="results">
			<?php foreach ($results as $fileName => $hits): ?>
				<div class="file">
					<h2>
						<?= htmlspecialchars($fileName, ENT_QUOTES, 'UTF-8') ?>
						<span>（<?= count($hits) ?> 筆）</span>
					</h2>
					<?php foreach ($hits as $hit): ?>
						<div class="hit">
							<span class="no">第 <?= $hit['line'] ?> 行</span>
							<span><?= highlight($hit['text'], $keyword) ?></span>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</body>

</html>
