<?php
/**
 * D11 Bar Chart
 * 直接 include 題目提供的 random.php 取得 $data（未修改 random.php 的任何邏輯），
 * 再於伺服器端輸出 SVG 長條圖。
 *
 * 需求對應：
 * - Y 軸刻度為整數且至少 5 個（含 0）
 * - 長條數量由 random.php 隨機決定（5 ~ 15）
 * - 每根長條顏色隨機
 * - X 軸下方顯示 name
 */

require __DIR__ . '/random.php'; // 產生 $data = [['name' => ..., 'value' => ...], ...]

/**
 * 將最大值向上取整到「好看的級距」，並回傳刻度陣列（整數、含 0）
 *
 * @param int $maxValue  資料中的最大值
 * @param int $tickCount 刻度間隔數（實際刻度數為 $tickCount + 1，因此至少 5 個）
 * @return array{0:int,1:array<int>} [Y 軸上限, 刻度值陣列（由小到大）]
 */
function buildYAxisTicks(int $maxValue, int $tickCount = 5): array
{
    if ($maxValue <= 0) {
        $maxValue = 1;
    }

    // 先算出「每格大約多少」，再向上取整到 1/2/5 × 10^n 這類漂亮的級距
    $rawStep = $maxValue / $tickCount;
    $magnitude = pow(10, floor(log10($rawStep)));
    $normalized = $rawStep / $magnitude;

    if ($normalized <= 1) {
        $niceStep = 1;
    } elseif ($normalized <= 2) {
        $niceStep = 2;
    } elseif ($normalized <= 5) {
        $niceStep = 5;
    } else {
        $niceStep = 10;
    }

    $step = (int) max(1, round($niceStep * $magnitude));

    // 確保上限可以蓋過最大值
    while ($step * $tickCount < $maxValue) {
        $step++;
    }

    $ticks = [];
    for ($i = 0; $i <= $tickCount; $i++) {
        $ticks[] = $step * $i;
    }

    return [$step * $tickCount, $ticks];
}

/**
 * 隨機產生一組長條顏色（淡色填滿 + 深色細邊框）
 *
 * @return array{fill:string,stroke:string}
 */
function randomBarColor(): array
{
    $hue = mt_rand(0, 359);

    return [
        // 淡色填滿：高亮度、中等飽和度
        'fill'   => sprintf('hsl(%d, 65%%, 78%%)', $hue),
        // 深色細邊框：同色系但更暗
        'stroke' => sprintf('hsl(%d, 45%%, 28%%)', $hue),
    ];
}

// ---------- 圖表尺寸設定 ----------
$chartWidth   = 900;
$chartHeight  = 600;
$paddingLeft  = 70;
$paddingRight = 30;
$paddingTop   = 30;
$paddingBottom = 70;

$plotWidth  = $chartWidth - $paddingLeft - $paddingRight;
$plotHeight = $chartHeight - $paddingTop - $paddingBottom;

$barCount = count($data);
$maxValue = 0;
foreach ($data as $item) {
    $maxValue = max($maxValue, (int) $item['value']);
}

[$yAxisMax, $yTicks] = buildYAxisTicks($maxValue, 5);

// 每個長條所佔的水平寬度，實際長條寬度取 60%（其餘為間距）
$slotWidth = $plotWidth / $barCount;
$barWidth  = $slotWidth * 0.6;
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bar Chart</title>
    <style>
        body {
            margin: 0;
            padding: 24px;
            background: #fff;
            color: #222;
            font-family: "Segoe UI", "Microsoft JhengHei", Arial, sans-serif;
        }

        .chart-wrapper {
            max-width: 960px;
            margin: 0 auto;
        }

        h1 {
            font-size: 22px;
            margin: 0 0 16px;
        }

        svg {
            width: 100%;
            height: auto;
        }

        /* 座標軸文字 */
        .axis-label {
            font-size: 17px;
            fill: #111;
        }

        .x-label {
            font-size: 17px;
            fill: #111;
            font-weight: 600;
        }

        .reload {
            display: inline-block;
            margin-top: 16px;
            padding: 8px 16px;
            border-radius: 6px;
            background: #2f6fed;
            color: #fff;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="chart-wrapper">
    <h1>Bar Chart（共 <?= $barCount ?> 筆資料，每次重新整理都會重新隨機）</h1>

    <svg viewBox="0 0 <?= $chartWidth ?> <?= $chartHeight ?>" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Bar chart">
        <?php // ---------- Y 軸刻度文字（整數，含 0，共 6 個） ---------- ?>
        <?php foreach ($yTicks as $tick): ?>
            <?php $y = $paddingTop + $plotHeight - ($tick / $yAxisMax) * $plotHeight; ?>
            <text class="axis-label"
                  x="<?= $paddingLeft - 12 ?>"
                  y="<?= round($y + 6, 2) ?>"
                  text-anchor="end"><?= $tick ?></text>
        <?php endforeach; ?>

        <?php // ---------- 長條 ---------- ?>
        <?php foreach ($data as $index => $item): ?>
            <?php
            $value = (int) $item['value'];
            $barHeight = ($value / $yAxisMax) * $plotHeight;
            $x = $paddingLeft + $index * $slotWidth + ($slotWidth - $barWidth) / 2;
            $y = $paddingTop + $plotHeight - $barHeight;
            $color = randomBarColor();
            ?>
            <rect x="<?= round($x, 2) ?>"
                  y="<?= round($y, 2) ?>"
                  width="<?= round($barWidth, 2) ?>"
                  height="<?= round($barHeight, 2) ?>"
                  fill="<?= $color['fill'] ?>"
                  stroke="<?= $color['stroke'] ?>"
                  stroke-width="1.5">
                <title><?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?>: <?= $value ?></title>
            </rect>

            <?php // ---------- X 軸資料標籤（顯示 name） ---------- ?>
            <text class="x-label"
                  x="<?= round($paddingLeft + $index * $slotWidth + $slotWidth / 2, 2) ?>"
                  y="<?= $paddingTop + $plotHeight + 28 ?>"
                  text-anchor="middle"><?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?></text>
        <?php endforeach; ?>

        <?php // ---------- 座標軸線 ---------- ?>
        <line x1="<?= $paddingLeft ?>" y1="<?= $paddingTop ?>"
              x2="<?= $paddingLeft ?>" y2="<?= $paddingTop + $plotHeight ?>"
              stroke="#000" stroke-width="3"/>
        <line x1="<?= $paddingLeft - 2 ?>" y1="<?= $paddingTop + $plotHeight ?>"
              x2="<?= $chartWidth - $paddingRight ?>" y2="<?= $paddingTop + $plotHeight ?>"
              stroke="#000" stroke-width="3"/>
    </svg>

    <a class="reload" href="">重新產生</a>
</div>

</body>
</html>
