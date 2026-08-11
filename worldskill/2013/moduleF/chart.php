<?php
/**
 * 動態產生「Performance by trade over the years」折線圖（PNG）。
 *
 * 參數：
 *   trade   職類編號
 *   country 國家 ISO，或 all 代表全部國家
 */
require_once __DIR__ . '/lib/bootstrap.php';

$trade   = isset($_GET['trade'])   ? trim($_GET['trade'])   : '';
$country = isset($_GET['country']) ? trim($_GET['country']) : '';

$chart = new LineChart();

if ($trade === '' || $country === '') {
    $chart->renderError('No trade or country was selected.');
    exit;
}

try {
    $gateway    = new StatisticsGateway();
    $repository = new StatisticsRepository($gateway);

    $skillOptions   = $repository->skillOptions();
    $countryOptions = $repository->countryOptions();

    if (!isset($skillOptions[$trade])) {
        $chart->renderError('The trade "' . $trade . '" is not available in the data source.');
        exit;
    }
    if ($country !== 'all' && !isset($countryOptions[$country])) {
        $chart->renderError('The country "' . $country . '" is not available in the data source.');
        exit;
    }

    $data = $repository->performanceByTrade($trade, $country);

    // 圖例需要國名；countryOptions 的值為 "ISO - Name"，這裡取出國名部分
    $names = array();
    foreach ($countryOptions as $iso => $label) {
        $names[$iso] = substr($label, strlen($iso) + 3);
    }

    $title = $skillOptions[$trade]
           . ' - ' . ($country === 'all' ? 'all countrys' : $countryOptions[$country]);

    $notice = empty($data['series'])
        ? 'No results available for this selection.'
        : '';

    $chart->render($title, Config::years(), $data['series'], $data['average'], $names, $notice);

} catch (RuntimeException $exception) {
    $chart->renderError($exception->getMessage());
}
