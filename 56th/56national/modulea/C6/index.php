<?php
/**
 * C6: 重組詞搜尋工具（等級 1）
 * 在 dictionary.txt 中找出與輸入單字字母組成完全相同的所有重組詞（anagram），
 * 輸入的單字本身不列入結果。
 */

declare(strict_types=1);

/**
 * 產生單字的「字母指紋」：轉小寫後把字母排序，
 * 兩個字是重組詞的充要條件就是指紋相同。
 */
function fingerprint(string $word): string
{
    $letters = str_split(strtolower(trim($word)));
    sort($letters);
    return implode('', $letters);
}

$word = trim((string) ($_GET['word'] ?? ''));
$matches = [];
$message = '';

if ($word !== '') {
    $dictionaryPath = __DIR__ . '/dictionary.txt';

    if (!is_file($dictionaryPath)) {
        $message = '找不到字典檔 dictionary.txt。';
    } else {
        $target = fingerprint($word);
        $lines = file($dictionaryPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];

        foreach ($lines as $line) {
            $candidate = trim($line);
            if ($candidate === '') {
                continue;
            }
            // 排除輸入單字本身（不分大小寫）
            if (strcasecmp($candidate, $word) === 0) {
                continue;
            }
            if (fingerprint($candidate) === $target) {
                $matches[] = $candidate;
            }
        }

        $matches = array_values(array_unique($matches));
        sort($matches);

        if ($matches === []) {
            $message = '字典中找不到「' . $word . '」的重組詞。';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-Hant">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>C6 重組詞搜尋工具</title>
	<style>
		body {
			margin: 0px;
			padding: 40px 20px;
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

		.hint {
			color: #59636e;
			font-size: 14px;
			margin: 0px;
		}

		form {
			display: flex;
			gap: 10px;
		}

		input[type="text"] {
			width: 280px;
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

		.result {
			width: min(90vw, 620px);
			background: #ffffff;
			border: 1px solid #d0d7de;
			border-radius: 10px;
			padding: 18px 22px;
		}

		.result h2 {
			font-size: 15px;
			margin: 0px 0px 12px 0px;
			color: #59636e;
			font-weight: normal;
		}

		ul {
			list-style: none;
			margin: 0px;
			padding: 0px;
			display: flex;
			flex-wrap: wrap;
			gap: 8px;
		}

		li {
			background: #eff6ff;
			border: 1px solid #cfe0fb;
			color: #1f4fa8;
			border-radius: 6px;
			padding: 6px 14px;
			font-size: 15px;
		}
	</style>
</head>

<body>
	<h1>重組詞搜尋工具</h1>
	<p class="hint">輸入一個單字，會在 dictionary.txt 中找出所有由相同字母重組而成的單字。</p>

	<form method="get">
		<input type="text" name="word" placeholder="例如：listen"
			value="<?= htmlspecialchars($word, ENT_QUOTES, 'UTF-8') ?>" required>
		<button type="submit">搜尋</button>
	</form>

	<?php if ($message !== ''): ?>
		<p class="hint"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
	<?php endif; ?>

	<?php if ($matches !== []): ?>
		<div class="result">
			<h2>「<?= htmlspecialchars($word, ENT_QUOTES, 'UTF-8') ?>」的重組詞，共 <?= count($matches) ?> 個</h2>
			<ul>
				<?php foreach ($matches as $match): ?>
					<li><?= htmlspecialchars($match, ENT_QUOTES, 'UTF-8') ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>
</body>

</html>
