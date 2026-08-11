<?php
/**
 * Module F 主畫面。
 *
 * 版面沿用題目提供的 StatisticsTemplate.html（標題、兩個區塊、下拉選單、
 * 表格結構與 .ok / .error 訊息樣式），再補上必要的排版。
 *
 * @var array       $countryOptions       ISO => "ISO - Name"
 * @var array       $skillOptions         number => "number - Name"
 * @var array|null  $tableRows            表格資料，null 表示尚未查詢
 * @var string      $tableError           表格錯誤訊息
 * @var string      $chartError           圖表錯誤訊息
 * @var bool        $chartReady           是否要顯示圖表
 * @var string      $selectedTableCountry 表格所選國家
 * @var string      $selectedTrade        圖表所選職類
 * @var string      $selectedChartCountry 圖表所選國家
 * @var string      $fatalError           資料來源完全無法使用時的訊息
 * @var string[]    $warnings             次要警告
 * @var string      $sourceLabel          實際使用的資料來源
 */
$years = Config::years();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>WorldSkills Statistics</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<h1>WorldSkills Statistics</h1>

<?php if ($fatalError !== ''): ?>
    <p class="error">The statistics data cannot be read at the moment: <?php echo e($fatalError); ?><br>
        Please check that the central web service is reachable and try again.</p>
<?php endif; ?>

<?php foreach ($warnings as $warning): ?>
    <p class="warning"><?php echo e($warning); ?></p>
<?php endforeach; ?>

<!-- ==================== 獎牌表 ==================== -->
<div id="MedalsByCountry">
    <h2>Medals by country over the years</h2>

    <form action="" method="post" name="medals" id="MedalsByCountryForm">
        <select name="countryTable" id="countrys">
            <option value="">Select a country</option>
            <?php foreach ($countryOptions as $iso => $label): ?>
                <option value="<?php echo e($iso); ?>"
                    <?php echo (string)$iso === $selectedTableCountry ? ' selected' : ''; ?>>
                    <?php echo e($label); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" name="showMedals" value="Show">Show</button>
        <span class="message">
            <?php if ($tableError !== ''): ?>
                <span class="error"><?php echo e($tableError); ?></span>
            <?php elseif ($tableRows !== null && empty($tableRows)): ?>
                <span class="ok">No awards found for
                    <?php echo e($countryOptions[$selectedTableCountry]); ?>.</span>
            <?php elseif ($tableRows !== null): ?>
                <span class="ok"><?php echo count($tableRows); ?> trade(s) with awards for
                    <?php echo e($countryOptions[$selectedTableCountry]); ?>.</span>
            <?php endif; ?>
        </span>
    </form>

    <?php if (!empty($tableRows)): ?>
        <table id="MedalsByCountryTable" border="1"
               summary="Select the country with the dropdown above">
            <caption>Medals by country over the years</caption>
            <thead>
            <tr>
                <th>Trade</th>
                <?php foreach ($years as $year): ?>
                    <th><?php echo e($year); ?></th>
                <?php endforeach; ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($tableRows as $row): ?>
                <tr>
                    <td><?php echo e($row['number'] . ' - ' . $row['name']); ?></td>
                    <?php foreach ($years as $year): ?>
                        <td><?php echo e($row['awards'][$year]); ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- ==================== 成績折線圖 ==================== -->
<div id="PerformanceOverYears">
    <h2>Performance by trade over the years</h2>

    <form action="" method="post" name="performance">
        <select name="trade">
            <option value="">Select a trade</option>
            <?php foreach ($skillOptions as $number => $label): ?>
                <?php // 陣列鍵在 PHP 中可能被轉成整數，比較前先轉回字串 ?>
                <option value="<?php echo e($number); ?>"
                    <?php echo (string)$number === $selectedTrade ? ' selected' : ''; ?>>
                    <?php echo e($label); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select name="countryChart">
            <option value="">Select a country</option>
            <option value="all"<?php echo $selectedChartCountry === 'all' ? ' selected' : ''; ?>>All countrys</option>
            <?php foreach ($countryOptions as $iso => $label): ?>
                <option value="<?php echo e($iso); ?>"
                    <?php echo (string)$iso === $selectedChartCountry ? ' selected' : ''; ?>>
                    <?php echo e($label); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit" name="showPerformance" value="Show">Show</button>
        <span class="message">
            <?php if ($chartError !== ''): ?>
                <span class="error"><?php echo e($chartError); ?></span>
            <?php endif; ?>
        </span>
    </form>

    <?php if ($chartReady): ?>
        <?php
        // 圖片由 chart.php 於伺服器端動態產生（PNG）
        $chartUrl = 'chart.php?trade=' . rawurlencode($selectedTrade)
                  . '&country=' . rawurlencode($selectedChartCountry)
                  . '&t=' . time();   // 避免瀏覽器使用舊的快取圖片
        ?>
        <img src="<?php echo e($chartUrl); ?>"
             alt="Performance of <?php echo e($skillOptions[$selectedTrade]); ?> over the years"
             class="diagram">
    <?php else: ?>
        <p class="hint">Select a trade and a country (or &laquo;All countrys&raquo;) and press
            &laquo;Show&raquo; to generate the diagram.</p>
    <?php endif; ?>
</div>

<?php if ($sourceLabel !== ''): ?>
    <p class="source">Data source: <?php echo e($sourceLabel); ?> &mdash;
        <a href="service/">open the central server</a></p>
<?php endif; ?>

</body>
</html>
