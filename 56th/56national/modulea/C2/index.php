<?php
/**
 * C2: CSV 資料解析與聚合（等級 2）
 * 讀取內建於程式中的 CSV 字串（至少含分類與數值兩欄），
 * 解析後計算每個分類的數值總和，並以可讀格式輸出。
 */

declare(strict_types=1);

// 內建的 CSV 內容：category（分類）、product（品項）、amount（數值）
$csv = <<<CSV
category,product,amount
Food,Rice,1200
Electronics,Laptop,35000
Food,Noodles,850
Clothing,T-Shirt,690
Electronics,Mouse,450
Food,Bread,320
Clothing,Jacket,2480
Electronics,Monitor,7800
Clothing,Socks,150
Food,Milk,560
CSV;

/**
 * 解析 CSV 字串
 * @param string $csv 原始 CSV 內容
 * @return array{header: string[], rows: array<int, string[]>}
 */
function parseCsv(string $csv): array
{
    $lines = preg_split('/\R/', trim($csv));
    $header = str_getcsv(array_shift($lines));
    $rows = [];
    foreach ($lines as $line) {
        if (trim($line) === '') {
            continue;
        }
        $rows[] = str_getcsv($line);
    }
    return ['header' => $header, 'rows' => $rows];
}

/**
 * 依分類欄加總數值欄
 * @param array<int, string[]> $rows      資料列
 * @param int                  $categoryIndex 分類欄的位置
 * @param int                  $valueIndex    數值欄的位置
 * @return array<string, float> 分類 => 總和
 */
function sumByCategory(array $rows, int $categoryIndex, int $valueIndex): array
{
    $totals = [];
    foreach ($rows as $row) {
        if (!isset($row[$categoryIndex], $row[$valueIndex])) {
            continue;
        }
        $category = trim($row[$categoryIndex]);
        $value = (float) $row[$valueIndex];
        $totals[$category] = ($totals[$category] ?? 0) + $value;
    }
    return $totals;
}

$parsed = parseCsv($csv);
$categoryIndex = array_search('category', $parsed['header'], true);
$valueIndex = array_search('amount', $parsed['header'], true);
$totals = sumByCategory($parsed['rows'], (int) $categoryIndex, (int) $valueIndex);

arsort($totals);              // 由大到小排序，閱讀上比較直覺
$grandTotal = array_sum($totals);
?>
<!DOCTYPE html>
<html lang="zh-Hant">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>C2 CSV 資料解析與聚合</title>
	<style>
		body {
			margin: 0px;
			min-height: 100vh;
			display: grid;
			place-items: center;
			background: #f6f8fa;
			font-family: "Microsoft JhengHei", Arial, Helvetica, sans-serif;
			color: #1f2328;
		}

		.card {
			background: #ffffff;
			border: 1px solid #d0d7de;
			border-radius: 12px;
			padding: 28px 34px;
			box-shadow: 0px 4px 16px rgba(0, 0, 0, 0.07);
			min-width: 420px;
		}

		h1 {
			font-size: 20px;
			margin: 0px 0px 4px 0px;
		}

		.hint {
			color: #59636e;
			font-size: 13px;
			margin: 0px 0px 20px 0px;
		}

		table {
			width: 100%;
			border-collapse: collapse;
		}

		th, td {
			padding: 9px 10px;
			border-bottom: 1px solid #e4e7ec;
			text-align: left;
		}

		th {
			background: #f6f8fa;
			font-size: 13px;
			color: #444d56;
		}

		td.value {
			text-align: right;
			font-variant-numeric: tabular-nums;
		}

		tr.total td {
			font-weight: bold;
			border-top: 2px solid #d0d7de;
			border-bottom: none;
		}

		pre {
			background: #0f1216;
			color: #e6edf3;
			font-size: 12px;
			padding: 14px;
			border-radius: 8px;
			overflow-x: auto;
			margin-top: 22px;
		}
	</style>
</head>

<body>
	<div class="card">
		<h1>CSV 分類加總結果</h1>
		<p class="hint">資料來源為程式內建的 CSV 字串，欄位為 category / product / amount。</p>

		<table>
			<thead>
				<tr>
					<th>分類 (category)</th>
					<th class="value">總和 (amount)</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($totals as $category => $sum): ?>
					<tr>
						<td><?= htmlspecialchars($category, ENT_QUOTES, 'UTF-8') ?></td>
						<td class="value"><?= number_format($sum) ?></td>
					</tr>
				<?php endforeach; ?>
				<tr class="total">
					<td>合計</td>
					<td class="value"><?= number_format($grandTotal) ?></td>
				</tr>
			</tbody>
		</table>

		<pre><?php
			// 同時以「分類名稱：總和」的純文字格式輸出一份
			foreach ($totals as $category => $sum) {
				echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8') . '：' . number_format($sum) . "\n";
			}
		?></pre>
	</div>
</body>

</html>
