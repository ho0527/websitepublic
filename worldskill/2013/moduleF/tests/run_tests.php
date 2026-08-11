<?php
/**
 * Module F 驗證腳本。
 *
 * 以「獨立實作的第二套計算」直接讀取中央伺服器的 XML 原始檔，
 * 再與應用程式經由 SOAP 取得並聚合後的結果比對，確認：
 *   1. 手寫的 SOAP 用戶端／服務端能完整往返
 *   2. 下拉選單的內容與排序正確
 *   3. 獎牌表與折線圖的數值正確
 *   4. 中央資料異動會即時反映到前端
 *   5. 資料來源異常時有妥善的錯誤處理
 *
 * 執行：php tests/run_tests.php
 */
require_once dirname(__DIR__) . '/lib/bootstrap.php';

$passed = 0;
$failed = 0;

/** 單一檢核 */
function check($description, $condition, $detail = '')
{
    global $passed, $failed;
    if ($condition) {
        $passed++;
        echo '  [PASS] ' . $description . ($detail === '' ? '' : ' -> ' . $detail) . PHP_EOL;
    } else {
        $failed++;
        echo '  [FAIL] ' . $description . ($detail === '' ? '' : ' -> ' . $detail) . PHP_EOL;
    }
}

/** 區段標題 */
function section($title)
{
    echo PHP_EOL . '== ' . $title . PHP_EOL;
}

// ---------------------------------------------------------------------------
// 另一套獨立實作：直接解析 XML 原始檔，作為比對基準
// ---------------------------------------------------------------------------

/**
 * 直接由 XML 檔讀出成績（不經過 SOAP）。
 *
 * @return array[] array('year','skill','country','medal','score')
 */
function readResultsDirectly()
{
    $document = new DOMDocument();
    $document->load(Config::servicePath() . '/data/WSC-Results.xml');
    $xpath = new DOMXPath($document);

    $rows = array();
    foreach ($xpath->query('//result') as $node) {
        $row = array();
        foreach ($node->childNodes as $child) {
            if ($child->nodeType === XML_ELEMENT_NODE) {
                $row[$child->nodeName] = trim($child->textContent);
            }
        }
        $rows[] = array(
            'year'    => isset($row['year']) ? $row['year'] : '',
            'skill'   => isset($row['skill-number']) ? $row['skill-number'] : '',
            'country' => isset($row['country-iso']) ? $row['country-iso'] : '',
            'medal'   => isset($row['medal']) ? $row['medal'] : '',
            'score'   => isset($row['score']) ? $row['score'] : '',
        );
    }
    return $rows;
}

$gateway    = new StatisticsGateway();
$repository = new StatisticsRepository($gateway);
$directRows = readResultsDirectly();

echo 'WorldSkills 2013 - Module F test suite' . PHP_EOL;
echo str_repeat('-', 70) . PHP_EOL;

// ---------------------------------------------------------------------------
section('1. SOAP round-trip');

$countrys = $gateway->countrys();
$skills   = $gateway->skills();
$results  = $gateway->results();

check('Transport used', $gateway->getSourceLabel() !== '', $gateway->getSourceLabel());
check('Countries received over SOAP', count($countrys) > 0, count($countrys) . ' countries');
check('Skills received over SOAP', count($skills) > 0, count($skills) . ' trades');
check('Results received over SOAP', count($results) === count($directRows),
      count($results) . ' results (XML file contains ' . count($directRows) . ')');

// 逐筆比對前 50 筆，確認 SOAP 傳輸沒有遺失或竄改資料
$mismatch = 0;
for ($i = 0; $i < min(50, count($results)); $i++) {
    if ($results[$i]['year'] !== $directRows[$i]['year']
        || $results[$i]['skill_number'] !== $directRows[$i]['skill']
        || $results[$i]['country_iso'] !== $directRows[$i]['country']
        || $results[$i]['points'] !== $directRows[$i]['score']
        || $results[$i]['award'] !== $directRows[$i]['medal']) {
        $mismatch++;
    }
}
check('First 50 results identical to the XML source', $mismatch === 0, $mismatch . ' mismatches');

// ---------------------------------------------------------------------------
section('2. Dropdown content and sorting');

$countryOptions = $repository->countryOptions();
$skillOptions   = $repository->skillOptions();

$isoList = array_keys($countryOptions);
$sorted  = $isoList;
sort($sorted);
check('Countries sorted by ISO code', $isoList === $sorted,
      implode(', ', array_slice($isoList, 0, 6)) . ' ...');
check('Country label shows "ISO - name"',
      isset($countryOptions['AU']) && $countryOptions['AU'] === 'AU - Australia',
      isset($countryOptions['AU']) ? $countryOptions['AU'] : '(missing)');

$numbers        = array_keys($skillOptions);
$numbers        = array_map('strval', $numbers);
$numericNumbers = array_values(array_filter($numbers, 'ctype_digit'));
$expectedOrder  = $numericNumbers;
usort($expectedOrder, function ($a, $b) { return (int)$a - (int)$b; });
check('Trades sorted by trade number', $numericNumbers === $expectedOrder,
      implode(', ', array_slice($numbers, 0, 6)) . ' ...');
check('Trade label shows "number - name"',
      isset($skillOptions['17']) && $skillOptions['17'] === '17 - Web Design',
      isset($skillOptions['17']) ? $skillOptions['17'] : '(missing)');
check('Duplicated trade numbers removed', count($numbers) === count(array_unique($numbers)),
      count($numbers) . ' unique trade numbers');

// ---------------------------------------------------------------------------
section('3. Table: medals by country');

foreach (array('AU', 'CH', 'TW') as $iso) {
    $rows = $repository->medalsByCountry($iso);

    // 獨立計算：直接從 XML 統計該國有獎項的 (職類, 年度)
    $expected = array();
    foreach ($directRows as $row) {
        if ($row['country'] === $iso && $row['medal'] !== ''
            && in_array((int)$row['year'], Config::years(), true)) {
            $expected[$row['skill'] . '/' . $row['year']] = $row['medal'];
        }
    }

    $actual = array();
    foreach ($rows as $row) {
        foreach ($row['awards'] as $year => $award) {
            if ($award !== '') {
                $actual[$row['number'] . '/' . $year] = $award;
            }
        }
    }

    ksort($expected);
    ksort($actual);
    check($iso . ': number of awards matches the XML source',
          count($expected) === count($actual),
          count($actual) . ' awards (expected ' . count($expected) . ')');

    // 比較內容（獎項名稱經過大小寫標準化）
    $normalised = array();
    foreach ($expected as $key => $medal) {
        $normalised[$key] = StatisticsRepository::awardLabel($medal);
    }
    check($iso . ': every award value matches', $normalised === $actual);

    // 排序：職類編號由小到大
    $order  = array();
    foreach ($rows as $row) {
        $order[] = $row['number'];
    }
    $order       = array_map('strval', $order);
    $sortedOrder = $order;
    usort($sortedOrder, function ($a, $b) {
        if (ctype_digit($a) && ctype_digit($b)) {
            return (int)$a - (int)$b;
        }
        if (ctype_digit($a) !== ctype_digit($b)) {
            return ctype_digit($a) ? -1 : 1;
        }
        return strcmp($a, $b);
    });
    check($iso . ': rows sorted by trade number', $order === $sortedOrder,
          implode(', ', array_slice($order, 0, 8)) . ' ...');
}

// 未知國家應得到空表格而非錯誤
$empty = $repository->medalsByCountry('XX');
check('Unknown country returns an empty table', $empty === array());

// ---------------------------------------------------------------------------
section('4. Diagram: performance by trade');

foreach (array('17', '13', '01') as $trade) {
    $data = $repository->performanceByTrade($trade, 'all');

    // 獨立計算各年度平均
    foreach (Config::years() as $year) {
        $sum = 0.0;
        $n   = 0;
        foreach ($directRows as $row) {
            if ($row['skill'] === $trade && (int)$row['year'] === $year && is_numeric($row['score'])) {
                $sum += (float)$row['score'];
                $n++;
            }
        }
        $expected = $n > 0 ? round($sum / $n, 1) : null;
        check('Trade ' . $trade . ' / ' . $year . ': average is correct',
              $data['average'][$year] === $expected,
              'got ' . var_export($data['average'][$year], true)
              . ', expected ' . var_export($expected, true) . ' (' . $n . ' competitors)');
    }

    // 每個國家的數值
    $wrong = 0;
    foreach ($data['series'] as $iso => $points) {
        foreach ($points as $year => $value) {
            $expected = null;
            foreach ($directRows as $row) {
                if ($row['skill'] === $trade && $row['country'] === $iso && (int)$row['year'] === $year) {
                    $expected = is_numeric($row['score']) ? (float)$row['score'] : null;
                }
            }
            if ($value !== $expected) {
                $wrong++;
            }
        }
    }
    check('Trade ' . $trade . ': every country data point is correct', $wrong === 0,
          count($data['series']) . ' countries, ' . $wrong . ' wrong values');
}

// 單一國家的篩選
$single = $repository->performanceByTrade('17', 'AU');
check('Filtering by one country returns exactly that country',
      array_keys($single['series']) === array('AU'),
      implode(', ', array_keys($single['series'])));
$all = $repository->performanceByTrade('17', 'all');
check('Average is identical for "one country" and "all countrys"',
      $single['average'] === $all['average'],
      json_encode($single['average']));
check('Unknown trade returns no series',
      $repository->performanceByTrade('ZZ', 'all')['series'] === array());

// ---------------------------------------------------------------------------
section('5. Changes on the central server propagate to the front-end');

$resultsFile = Config::servicePath() . '/data/WSC-Results.xml';
$backup      = file_get_contents($resultsFile);

$before = $repository->performanceByTrade('17', 'AU');

// 修改中央資料：把 AU 在 2011 年 17 職類的分數改成 999
$modified = preg_replace(
    '#(<result>\s*<year>2011</year>\s*<skill-number>17</skill-number>\s*<country-iso>AU</country-iso>.*?<score>)\d+(</score>)#s',
    '${1}999${2}', $backup, 1, $replacements);
file_put_contents($resultsFile, $modified);

$freshGateway    = new StatisticsGateway();
$freshRepository = new StatisticsRepository($freshGateway);
$after = $freshRepository->performanceByTrade('17', 'AU');

check('A score changed on the central server is visible immediately',
      $replacements === 1 && $after['series']['AU'][2011] === 999.0,
      'before ' . var_export($before['series']['AU'][2011], true)
      . ', after ' . var_export(isset($after['series']['AU'][2011]) ? $after['series']['AU'][2011] : null, true));

// 新增一筆資料
$withExtra = str_replace('</wsc-results>',
    "\t<result>\n\t\t<year>2011</year>\n\t\t<skill-number>17</skill-number>\n"
    . "\t\t<country-iso>ZW</country-iso>\n\t\t<medal>GOLD</medal>\n\t\t<score>600</score>\n\t</result>\n</wsc-results>",
    $modified);
file_put_contents($resultsFile, $withExtra);

$freshGateway    = new StatisticsGateway();
$freshRepository = new StatisticsRepository($freshGateway);
$added = $freshRepository->performanceByTrade('17', 'all');

check('A result added on the central server appears in the diagram data',
      isset($added['series']['ZW']) && $added['series']['ZW'][2011] === 600.0,
      'ZW 2011 = ' . var_export(isset($added['series']['ZW'][2011]) ? $added['series']['ZW'][2011] : null, true));

// 還原
file_put_contents($resultsFile, $backup);
$restoredGateway    = new StatisticsGateway();
$restoredRepository = new StatisticsRepository($restoredGateway);
$restored = $restoredRepository->performanceByTrade('17', 'AU');
check('Central data restored', $restored['series']['AU'] == $before['series']['AU']);

// ---------------------------------------------------------------------------
section('6. Error handling');

// 不存在的操作 -> SOAP Fault
require_once Config::servicePath() . '/SoapEndpoint.php';
$endpoint = new SoapEndpoint(Config::servicePath() . '/data');

$fault = $endpoint->handle('<?xml version="1.0"?><SOAP-ENV:Envelope '
    . 'xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="urn:WSCstats">'
    . '<SOAP-ENV:Body><ns1:doesNotExist/></SOAP-ENV:Body></SOAP-ENV:Envelope>');
check('Unknown operation returns a SOAP fault',
      $fault['status'] === 500 && strpos($fault['body'], 'Unknown operation') !== false);

$fault = $endpoint->handle('this is not xml');
check('Malformed request returns a SOAP fault',
      $fault['status'] === 500 && strpos($fault['body'], 'not well-formed') !== false);

$fault = $endpoint->handle('<?xml version="1.0"?><SOAP-ENV:Envelope '
    . 'xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="urn:WSCstats">'
    . '<SOAP-ENV:Body><ns1:getList><listType>nonsense</listType></ns1:getList>'
    . '</SOAP-ENV:Body></SOAP-ENV:Envelope>');
check('Unknown listType returns a SOAP fault',
      $fault['status'] === 500 && strpos($fault['body'], 'Unknown listType') !== false);

// 用戶端遇到 Fault 時要丟出 SoapFaultException
$client = new SoapClientLite(new LoopbackTransport(Config::servicePath()), Config::SOAP_NAMESPACE);
$caught = false;
try {
    $client->call('getList', array('listType' => 'nonsense'));
} catch (SoapFaultException $exception) {
    $caught = true;
}
check('Client converts a SOAP fault into an exception', $caught);

// 連不上服務時要丟出 SoapTransportException
$caught = false;
try {
    $broken = new SoapClientLite(new HttpTransport('http://127.0.0.1:9/none', 1), Config::SOAP_NAMESPACE);
    $broken->call('getResults');
} catch (SoapTransportException $exception) {
    $caught = true;
}
check('Client reports an unreachable service as a transport error', $caught);

// 資料檔損毀時，閘道要能提出警告並改用備援
$skillsFile   = Config::servicePath() . '/data/WSC-Skills.xml';
$skillsBackup = file_get_contents($skillsFile);
file_put_contents($skillsFile, '<broken><unclosed></broken>');

$brokenGateway = new StatisticsGateway();
$brokenError   = '';
try {
    $brokenGateway->skills();
} catch (RuntimeException $exception) {
    $brokenError = $exception->getMessage();
}
file_put_contents($skillsFile, $skillsBackup);
check('A corrupt data file produces a readable error message', $brokenError !== '', $brokenError);

// ---------------------------------------------------------------------------
echo PHP_EOL . str_repeat('-', 70) . PHP_EOL;
echo 'PASSED: ' . $passed . '   FAILED: ' . $failed . PHP_EOL;
exit($failed === 0 ? 0 : 1);
