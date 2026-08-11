<?php
/**
 * WorldSkills 2013 - Skill 17 - Module F
 * WorldSkills Statistics：以中央 Web Service 的資料呈現獎牌表與成績折線圖。
 *
 * 進入點：處理表單參數、取得資料、再交由 views/ 底下的樣板輸出。
 */
require_once __DIR__ . '/lib/bootstrap.php';

$gateway    = new StatisticsGateway();
$repository = new StatisticsRepository($gateway);

// 使用者選擇（表格與圖表各自獨立）
$selectedTableCountry = isset($_POST['countryTable']) ? trim($_POST['countryTable']) : '';
$selectedTrade        = isset($_POST['trade'])        ? trim($_POST['trade'])        : '';
$selectedChartCountry = isset($_POST['countryChart']) ? trim($_POST['countryChart']) : '';

$countryOptions = array();
$skillOptions   = array();
$tableRows      = null;     // null 代表尚未查詢，表格保持隱藏
$tableError     = '';
$fatalError     = '';

try {
    $countryOptions = $repository->countryOptions();
    $skillOptions   = $repository->skillOptions();
} catch (RuntimeException $exception) {
    $fatalError = $exception->getMessage();
}

// 只有在使用者送出「Medals by country」表單後才建立表格
if ($fatalError === '' && isset($_POST['showMedals'])) {
    if ($selectedTableCountry === '' || !isset($countryOptions[$selectedTableCountry])) {
        $tableError = 'Please select a country first.';
    } else {
        try {
            $tableRows = $repository->medalsByCountry($selectedTableCountry);
        } catch (RuntimeException $exception) {
            $tableError = 'The awards could not be loaded: ' . $exception->getMessage();
        }
    }
}

// 圖表的驗證（圖片本身由 chart.php 產生）
$chartError = '';
$chartReady = false;
if ($fatalError === '' && isset($_POST['showPerformance'])) {
    if ($selectedTrade === '' || !isset($skillOptions[$selectedTrade])) {
        $chartError = 'Please select a trade first.';
    } elseif ($selectedChartCountry === '') {
        $chartError = 'Please select a country, or "All countrys".';
    } elseif ($selectedChartCountry !== 'all' && !isset($countryOptions[$selectedChartCountry])) {
        $chartError = 'The selected country is not available.';
    } else {
        $chartReady = true;
    }
}

$warnings    = $gateway->getWarnings();
$sourceLabel = $gateway->getSourceLabel();

require __DIR__ . '/views/page.php';
