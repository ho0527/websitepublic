<?php
/**
 * WorldSkills 2024 模組 D - 模擬資料 API 伺服器（Mock API Server）
 *
 * 題目原本提供 dl.worldskills.org/module_d_api_server.zip 內的 module_d_api.php，
 * 但本機為離線環境無法下載，因此依照題目規格自行重建一份等效的模擬 API。
 *
 * 三個資源（與題目規格相同）：
 *   module_d_api.php/carparks.json   停車場即時空位
 *   module_d_api.php/events.json     里昂活動列表（支援分頁與日期區間查詢）
 *   module_d_api.php/weather.json    未來一週天氣
 *
 * 由於本機 nginx 的 location ~ \.php$ 規則不會把 /xxx.php/yyy.json 交給 PHP 處理，
 * 所以同時支援查詢字串形式：module_d_api.php?path=carparks.json
 * 兩種寫法回傳完全相同的內容，前端會自動選用可用的形式。
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-store');

/** 每頁活動筆數 */
const EVENTS_PER_PAGE = 8;

/** 停車場空位數的變動週期（秒），讓詳細頁每 10 秒重新整理時看得到數字變化 */
const AVAILABILITY_TICK_SECONDS = 10;

/**
 * 取得目前要求的資源名稱。
 * 優先讀 PATH_INFO（/module_d_api.php/events.json），
 * 找不到時改讀查詢字串 path / resource 參數。
 *
 * @return string 例如 "events.json"
 */
function resolveRequestedResource(): string
{
    $pathInfo = $_SERVER['PATH_INFO'] ?? '';

    if ($pathInfo === '' && isset($_SERVER['REQUEST_URI'])) {
        // 部分伺服器不會提供 PATH_INFO，改由 REQUEST_URI 自行解析
        $requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '';
        if (preg_match('#module_d_api\.php/(.+)$#', $requestPath, $matches) === 1) {
            $pathInfo = '/' . $matches[1];
        }
    }

    if ($pathInfo !== '') {
        return ltrim($pathInfo, '/');
    }

    $queryResource = $_GET['path'] ?? $_GET['resource'] ?? '';
    return is_string($queryResource) ? ltrim($queryResource, '/') : '';
}

/**
 * 輸出 JSON 並結束程式。
 *
 * @param array<string,mixed> $payload    要輸出的資料
 * @param int                 $statusCode HTTP 狀態碼
 */
function respondJson(array $payload, int $statusCode = 200): void
{
    http_response_code($statusCode);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    exit;
}

/**
 * 以字串產生穩定（可重現）的偽亂數整數，避免每次請求資料都跳動。
 *
 * @param string $seed 種子字串
 * @param int    $min  最小值
 * @param int    $max  最大值
 */
function stableRandomInt(string $seed, int $min, int $max): int
{
    $hash = crc32($seed);
    return $min + (int)($hash % ($max - $min + 1));
}

/**
 * 停車場靜態基本資料（里昂市區實際停車場位置）。
 *
 * @return array<int,array<string,mixed>>
 */
function getCarparkBaseData(): array
{
    return [
        ['id' => 'LPA001', 'name' => 'Parking Bellecour',            'address' => 'Place Bellecour, 69002 Lyon',        'latitude' => 45.757430, 'longitude' => 4.832160, 'capacity' => 780],
        ['id' => 'LPA002', 'name' => 'Parking Celestins',            'address' => 'Place des Celestins, 69002 Lyon',    'latitude' => 45.759240, 'longitude' => 4.831820, 'capacity' => 430],
        ['id' => 'LPA003', 'name' => 'Parking Republique',           'address' => 'Rue de la Republique, 69002 Lyon',   'latitude' => 45.762510, 'longitude' => 4.836540, 'capacity' => 620],
        ['id' => 'LPA004', 'name' => 'Parking Terreaux',             'address' => 'Place des Terreaux, 69001 Lyon',     'latitude' => 45.767620, 'longitude' => 4.833910, 'capacity' => 510],
        ['id' => 'LPA005', 'name' => 'Parking Cordeliers',           'address' => 'Rue Grenette, 69002 Lyon',           'latitude' => 45.763920, 'longitude' => 4.835830, 'capacity' => 340],
        ['id' => 'LPA006', 'name' => 'Parking Saint-Antoine',        'address' => 'Quai Saint-Antoine, 69002 Lyon',     'latitude' => 45.760520, 'longitude' => 4.832980, 'capacity' => 290],
        ['id' => 'LPA007', 'name' => 'Parking Fosse aux Ours',       'address' => 'Cours de la Liberte, 69003 Lyon',    'latitude' => 45.754620, 'longitude' => 4.840230, 'capacity' => 460],
        ['id' => 'LPA008', 'name' => 'Parking Part-Dieu',            'address' => 'Rue du Docteur Bouchut, 69003 Lyon', 'latitude' => 45.761430, 'longitude' => 4.857050, 'capacity' => 1250],
        ['id' => 'LPA009', 'name' => 'Parking Villette',             'address' => 'Rue de la Villette, 69003 Lyon',     'latitude' => 45.760510, 'longitude' => 4.862040, 'capacity' => 700],
        ['id' => 'LPA010', 'name' => 'Parking Gare de Perrache',     'address' => 'Cours de Verdun, 69002 Lyon',        'latitude' => 45.749540, 'longitude' => 4.826580, 'capacity' => 890],
        ['id' => 'LPA011', 'name' => 'Parking Halles Paul Bocuse',   'address' => 'Cours Lafayette, 69003 Lyon',        'latitude' => 45.763810, 'longitude' => 4.850290, 'capacity' => 380],
        ['id' => 'LPA012', 'name' => 'Parking Saint-Jean',           'address' => 'Quai Romain Rolland, 69005 Lyon',    'latitude' => 45.760430, 'longitude' => 4.827120, 'capacity' => 260],
        ['id' => 'LPA013', 'name' => 'Parking Antonin Poncet',       'address' => 'Place Antonin Poncet, 69002 Lyon',   'latitude' => 45.756580, 'longitude' => 4.834070, 'capacity' => 420],
        ['id' => 'LPA014', 'name' => 'Parking Hotel de Ville',       'address' => 'Rue de la Republique, 69001 Lyon',   'latitude' => 45.767240, 'longitude' => 4.836260, 'capacity' => 550],
        ['id' => 'LPA015', 'name' => 'Parking Confluence',           'address' => 'Cours Charlemagne, 69002 Lyon',      'latitude' => 45.741420, 'longitude' => 4.817580, 'capacity' => 1100],
        ['id' => 'LPA016', 'name' => 'Parking Croix-Rousse',         'address' => 'Boulevard de la Croix-Rousse, 69004','latitude' => 45.774610, 'longitude' => 4.831930, 'capacity' => 310],
    ];
}

/**
 * 產生停車場即時空位資料。
 * 空位數每 10 秒變動一次，方便驗收「詳細頁每 10 秒更新」的需求。
 *
 * @return array<string,mixed>
 */
function buildCarparksResponse(): array
{
    $tick = (int)floor(time() / AVAILABILITY_TICK_SECONDS);
    $carparks = [];

    foreach (getCarparkBaseData() as $carpark) {
        $capacity  = (int)$carpark['capacity'];
        $available = stableRandomInt($carpark['id'] . '-' . $tick, 0, $capacity);

        $carpark['available'] = $available;
        $carpark['occupied']  = $capacity - $available;
        $carpark['status']    = $available === 0 ? 'full' : ($available < $capacity * 0.1 ? 'almost_full' : 'open');
        $carparks[] = $carpark;
    }

    return [
        'updated_at' => date('c'),
        'total'      => count($carparks),
        'carparks'   => $carparks,
    ];
}

/**
 * 產生完整的活動清單（固定資料，方便驗證分頁不重複、不遺漏）。
 *
 * @return array<int,array<string,mixed>>
 */
function getAllEvents(): array
{
    $titles = [
        'Fete des Lumieres', 'Nuits de Fourviere', 'Lyon Street Food Festival',
        'Biennale de la Danse', 'Quais du Polar', 'Nuits Sonores',
        'Festival Lumiere', 'Lyon BD Festival', 'Tout Le Monde Dehors',
        'Marche de Noel Lyon', 'Journees du Patrimoine', 'Roule Ma Frite',
        'Concert Auditorium de Lyon', 'Exposition Musee des Confluences',
        'Match OL Groupama Stadium', 'Salon du Chocolat Lyon',
        'Peinture Contemporaine MAC', 'Course de la Presqu ile',
        'Marathon de Lyon', 'Festival Woodstower',
    ];
    $venues = [
        'Place des Terreaux', 'Theatre Antique de Fourviere', 'La Sucriere',
        'Opera de Lyon', 'Palais du Commerce', 'Halle Tony Garnier',
        'Musee des Confluences', 'Parc de la Tete d Or', 'Hotel de Ville',
        'Groupama Stadium',
    ];
    $categories = ['Festival', 'Music', 'Exhibition', 'Sport', 'Food', 'Family'];
    $images = [
        'material/image/example n1.jpg',
        'material/image/example n2.jpg',
        'material/image/example n3.jpg',
        'material/image/example n4.jpg',
        'material/image/example-n5.jpg',
        'material/image/example-n6.jpg',
        'material/image/example-n7.jpg',
    ];

    $events = [];
    $startTimestamp = strtotime('today -30 days');

    // 產生 90 筆活動，日期自今天前 30 天起，每筆間隔 1 天（同一天可能有多筆）
    for ($index = 0; $index < 90; $index++) {
        $eventDate  = date('Y-m-d', $startTimestamp + (int)floor($index * 0.8) * 86400);
        $startHour  = 9 + ($index % 10);
        $identifier = 'EVT' . str_pad((string)($index + 1), 3, '0', STR_PAD_LEFT);

        $events[] = [
            'id'          => $identifier,
            'title'       => $titles[$index % count($titles)] . ' #' . ($index + 1),
            'date'        => $eventDate,
            'start_time'  => sprintf('%02d:00', $startHour),
            'end_time'    => sprintf('%02d:00', min(23, $startHour + 3)),
            'venue'       => $venues[$index % count($venues)],
            'category'    => $categories[$index % count($categories)],
            'image'       => $images[$index % count($images)],
            'description' => 'A highlighted Lyon event happening at ' . $venues[$index % count($venues)] . '.',
        ];
    }

    return $events;
}

/**
 * 依照分頁與日期區間條件輸出活動列表。
 *
 * @return array<string,mixed>
 */
function buildEventsResponse(): array
{
    $requestedPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $beginningDate = isValidDate($_GET['beginning_date'] ?? '') ? (string)$_GET['beginning_date'] : '';
    $endingDate    = isValidDate($_GET['ending_date'] ?? '') ? (string)$_GET['ending_date'] : '';

    // 依日期區間過濾
    $filteredEvents = array_values(array_filter(
        getAllEvents(),
        static function (array $event) use ($beginningDate, $endingDate): bool {
            if ($beginningDate !== '' && $event['date'] < $beginningDate) {
                return false;
            }
            if ($endingDate !== '' && $event['date'] > $endingDate) {
                return false;
            }
            return true;
        }
    ));

    $totalCount = count($filteredEvents);
    $totalPages = max(1, (int)ceil($totalCount / EVENTS_PER_PAGE));
    $pageEvents = array_slice($filteredEvents, ($requestedPage - 1) * EVENTS_PER_PAGE, EVENTS_PER_PAGE);

    return [
        'page'        => $requestedPage,
        'per_page'    => EVENTS_PER_PAGE,
        'total'       => $totalCount,
        'total_pages' => $totalPages,
        'pages'       => [
            'next' => $requestedPage < $totalPages ? buildPageUrl($requestedPage + 1, $beginningDate, $endingDate) : null,
            'prev' => $requestedPage > 1 ? buildPageUrl($requestedPage - 1, $beginningDate, $endingDate) : null,
        ],
        'events'      => $pageEvents,
    ];
}

/**
 * 檢查字串是否為合法的 YYYY-MM-DD 日期。
 *
 * @param mixed $value 待檢查的值
 */
function isValidDate(mixed $value): bool
{
    if (!is_string($value) || preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) !== 1) {
        return false;
    }
    [$year, $month, $day] = array_map('intval', explode('-', $value));
    return checkdate($month, $day, $year);
}

/**
 * 組出下一頁 / 上一頁的完整網址，保持與目前請求相同的呼叫形式。
 *
 * @param int    $pageNumber    頁碼
 * @param string $beginningDate 起始日期（空字串代表不限）
 * @param string $endingDate    結束日期（空字串代表不限）
 */
function buildPageUrl(int $pageNumber, string $beginningDate, string $endingDate): string
{
    $scriptPath = $_SERVER['SCRIPT_NAME'] ?? '/module_d_api.php';
    $usesPathInfo = ($_SERVER['PATH_INFO'] ?? '') !== '';

    $queryParameters = ['page' => $pageNumber];
    if (!$usesPathInfo) {
        $queryParameters = ['path' => 'events.json'] + $queryParameters;
    }
    if ($beginningDate !== '') {
        $queryParameters['beginning_date'] = $beginningDate;
    }
    if ($endingDate !== '') {
        $queryParameters['ending_date'] = $endingDate;
    }

    $basePath = $usesPathInfo ? $scriptPath . '/events.json' : $scriptPath;
    return $basePath . '?' . http_build_query($queryParameters);
}

/**
 * 產生未來 7 天的天氣資料。
 * icon 欄位即為前端要繪製的 SVG 圖示代號。
 *
 * @return array<string,mixed>
 */
function buildWeatherResponse(): array
{
    /** icon 代號 => 文字描述 */
    $weatherConditions = [
        'sunny'         => 'Sunny',
        'partly-cloudy' => 'Partly cloudy',
        'cloudy'        => 'Cloudy',
        'rain'          => 'Rain showers',
        'thunderstorm'  => 'Thunderstorm',
        'snow'          => 'Snow',
        'fog'           => 'Fog',
    ];
    $conditionKeys = array_keys($weatherConditions);

    $dailyForecast = [];
    for ($dayOffset = 0; $dayOffset < 7; $dayOffset++) {
        $timestamp = strtotime("today +{$dayOffset} days");
        $date      = date('Y-m-d', $timestamp);
        $iconKey   = $conditionKeys[stableRandomInt('weather-' . $date, 0, count($conditionKeys) - 1)];
        $highTemp  = stableRandomInt('high-' . $date, 14, 32);

        $dailyForecast[] = [
            'date'             => $date,
            'weekday'          => date('l', $timestamp),
            'weekday_short'    => date('D', $timestamp),
            'icon'             => $iconKey,
            'condition'        => $weatherConditions[$iconKey],
            'temperature_high' => $highTemp,
            'temperature_low'  => $highTemp - stableRandomInt('low-' . $date, 4, 9),
            'humidity'         => stableRandomInt('humidity-' . $date, 35, 92),
            'wind_speed'       => stableRandomInt('wind-' . $date, 3, 28),
            'precipitation'    => stableRandomInt('rain-' . $date, 0, 80),
        ];
    }

    return [
        'city'       => 'Lyon',
        'country'    => 'France',
        'updated_at' => date('c'),
        'daily'      => $dailyForecast,
    ];
}

// ---- 路由分派 ----
switch (resolveRequestedResource()) {
    case 'carparks.json':
        respondJson(buildCarparksResponse());
        break;

    case 'events.json':
        respondJson(buildEventsResponse());
        break;

    case 'weather.json':
        respondJson(buildWeatherResponse());
        break;

    default:
        respondJson([
            'error'     => 'Unknown resource',
            'resources' => ['carparks.json', 'events.json', 'weather.json'],
        ], 404);
}
