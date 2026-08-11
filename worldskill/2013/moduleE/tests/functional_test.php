<?php
/**
 * Module E 端對端功能測試。
 *
 * 以真正的 HTTP 請求操作應用程式（登入、建立、指派、驗證、刪除），
 * 並在結束後把自己建立的資料清乾淨，因此可以重複執行。
 *
 * 執行：php tests/functional_test.php
 * 前置：已執行過 setup/install.php，且 nginx 在 http://127.0.0.1:83/
 */

const BASE_URL = 'http://127.0.0.1:83/worldskill/2013/moduleE/index.php';

$cookieJar = sys_get_temp_dir() . '/ws2013_module_e_cookies.txt';
@unlink($cookieJar);

$passed = 0;
$failed = 0;

// ---------------------------------------------------------------------------
// HTTP 輔助函式
// ---------------------------------------------------------------------------

/**
 * 送出請求。
 *
 * @param string $path      相對於 BASE_URL 的路徑
 * @param array|null $fields POST 欄位；null 表示 GET
 * @param bool   $multipart 是否以 multipart/form-data 送出（上傳檔案時使用）
 * @return array array('status' => int, 'location' => string, 'body' => string)
 */
function request($path, $fields = null, $multipart = false)
{
    global $cookieJar;

    $handle = curl_init(BASE_URL . $path);
    curl_setopt_array($handle, array(
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER         => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_COOKIEJAR      => $cookieJar,
        CURLOPT_COOKIEFILE     => $cookieJar,
        CURLOPT_TIMEOUT        => 30,
    ));

    if ($fields !== null) {
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $multipart ? $fields : http_build_query($fields));
    }

    $response   = curl_exec($handle);
    $status     = (int)curl_getinfo($handle, CURLINFO_HTTP_CODE);
    $headerSize = (int)curl_getinfo($handle, CURLINFO_HEADER_SIZE);
    curl_close($handle);

    $headers = substr($response, 0, $headerSize);
    $body    = substr($response, $headerSize);

    $location = '';
    if (preg_match('/^Location:\s*(.+)$/mi', $headers, $matches)) {
        $location = trim($matches[1]);
    }

    return array('status' => $status, 'location' => $location, 'body' => $body);
}

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

/** 回應中是否包含指定訊息（HTML 已跳脫，比對時一併處理） */
function contains($body, $needle)
{
    return strpos($body, $needle) !== false
        || strpos($body, htmlspecialchars($needle, ENT_QUOTES)) !== false;
}

/** 直接查資料庫，驗證伺服器端的實際狀態 */
function db()
{
    static $connection = null;
    if ($connection === null) {
        $connection = new mysqli('127.0.0.1', 'root', '', 'ws2013_lvb', 3306);
        $connection->set_charset('utf8mb4');
    }
    return $connection;
}

/** 取單一數值 */
function scalar($sql)
{
    $result = db()->query($sql);
    $row    = $result->fetch_row();
    return $row === null ? null : $row[0];
}

echo 'WorldSkills 2013 - Module E functional test' . PHP_EOL;
echo str_repeat('-', 70) . PHP_EOL;

// ---------------------------------------------------------------------------
section('1. Authentication');

$response = request('/line/index');
check('A guest is redirected to the login page',
      $response['status'] === 302 && strpos($response['location'], 'site/login') !== false,
      'HTTP ' . $response['status']);

$response = request('/site/login', array(
    'LoginForm[username]' => 'webmaster',
    'LoginForm[password]' => 'wrong-password',
));
check('A wrong password is rejected', contains($response['body'], 'Incorrect login or password'));

$response = request('/site/login', array(
    'LoginForm[username]' => 'webmaster',
    'LoginForm[password]' => 'leipzig',
));
check('webmaster / leipzig can log in',
      $response['status'] === 302 && strpos($response['location'], 'site/index') !== false);

$response = request('/line/index');
check('The main menu is visible once logged in',
      $response['status'] === 200 && contains($response['body'], 'Intermediate Lines'));

// ---------------------------------------------------------------------------
section('2. Create stations and a vehicle');

$createdStations = array();
for ($i = 1; $i <= 8; $i++) {
    $name     = 'TEST Station ' . $i . ' ' . uniqid();
    $response = request('/station/create', array('Station[name]' => $name));
    $id       = scalar("SELECT id FROM station WHERE name = '" . db()->real_escape_string($name) . "'");
    if ($id !== null) {
        $createdStations[] = (int)$id;
    }
}
check('8 new stations created', count($createdStations) === 8,
      count($createdStations) . ' stations');

$vehicleName = 'TEST-BUS-' . strtoupper(substr(uniqid(), -6));
request('/vehicle/create', array(
    'Vehicle[name]'     => $vehicleName,
    'Vehicle[capacity]' => 90,
    'Vehicle[type]'     => 'Autobus',
));
$vehicleId = (int)scalar("SELECT id FROM vehicle WHERE name = '" . db()->real_escape_string($vehicleName) . "'");
check('A new vehicle created', $vehicleId > 0, $vehicleName . ' (id ' . $vehicleId . ')');

$tramName = 'TEST-TRAM-' . strtoupper(substr(uniqid(), -6));
request('/vehicle/create', array(
    'Vehicle[name]'     => $tramName,
    'Vehicle[capacity]' => 200,
    'Vehicle[type]'     => 'Tram',
));
$tramId = (int)scalar("SELECT id FROM vehicle WHERE name = '" . db()->real_escape_string($tramName) . "'");
check('A tram created for the type-mismatch test', $tramId > 0);

// ---------------------------------------------------------------------------
section('3. Create an Intermediate Line with a route map');

$lineCode = 'TEST Line ' . strtoupper(substr(uniqid(), -6));
$mapPath  = dirname(__DIR__) . '/assets/routes/Line_A33.svg';

$response = request('/line/create', array(
    'Line[code]'                 => $lineCode,
    'Line[start_time_operation]' => '08:00:00',
    'Line[end_time_operation]'   => '16:00:00',
    'Line[type]'                 => 'Autobus',
    'Line[mapFile]'              => new CURLFile($mapPath, 'image/svg+xml', 'Line_A33.svg'),
), true);

$lineId = (int)scalar("SELECT id FROM `line` WHERE code = '" . db()->real_escape_string($lineCode) . "'");
check('The line was created and the user sent to the stations form',
      $lineId > 0 && strpos($response['location'], 'line/stations') !== false,
      $lineCode . ' (id ' . $lineId . ')');

$mapFile = scalar("SELECT map FROM `line` WHERE id = " . $lineId);
check('The uploaded route map is stored on the server',
      $mapFile !== '' && is_file(dirname(__DIR__) . '/uploads/maps/' . $mapFile),
      (string)$mapFile);

$response = request('/line/create', array(
    'Line[code]'                 => $lineCode,
    'Line[start_time_operation]' => '08:00:00',
    'Line[end_time_operation]'   => '16:00:00',
    'Line[type]'                 => 'Autobus',
));
check('A duplicated line name is rejected',
      contains($response['body'], 'has already been taken'));

// ---------------------------------------------------------------------------
section('4. Assign exactly seven stations');

$seven = array_slice($createdStations, 0, 7);
$spare = $createdStations[7];

$response = request('/line/stations/' . $lineId, array(
    'StationSlots' => array($seven[0], $seven[1], '', '', '', '', ''),
));
check('An incomplete selection is rejected (all 7 at the same time)',
      contains($response['body'], 'must be selected at the same time'));

$response = request('/line/stations/' . $lineId, array(
    'StationSlots' => array($seven[0], $seven[0], $seven[2], $seven[3], $seven[4], $seven[5], $seven[6]),
));
check('The same station twice in one line is rejected',
      contains($response['body'], 'only once in a line'));

// 站點 id 1 屬於示範資料的 Line T21
$response = request('/line/stations/' . $lineId, array(
    'StationSlots' => array(1, $seven[1], $seven[2], $seven[3], $seven[4], $seven[5], $seven[6]),
));
check('A station that already belongs to another line is rejected',
      contains($response['body'], 'already belongs to another Intermediate Line'));

$response = request('/line/stations/' . $lineId, array('StationSlots' => $seven));
check('Seven free stations are accepted',
      $response['status'] === 302 && strpos($response['location'], 'line/' . $lineId) !== false);

check('The line now has exactly 7 stations',
      (int)scalar('SELECT COUNT(*) FROM station WHERE line_id = ' . $lineId) === 7);
check('The start station is stored with position START',
      (int)scalar("SELECT COUNT(*) FROM station WHERE line_id = $lineId AND position_station = 'START'") === 1);
check('The end station is stored with position END',
      (int)scalar("SELECT COUNT(*) FROM station WHERE line_id = $lineId AND position_station = 'END'") === 1);
check('Five intermediate stations are stored',
      (int)scalar("SELECT COUNT(*) FROM station WHERE line_id = $lineId AND position_station = 'INTER'") === 5);

// 未使用的第 8 個站點仍然是空閒的
check('An unused station stays free',
      (int)scalar('SELECT line_id FROM station WHERE id = ' . $spare) === 0);

// ---------------------------------------------------------------------------
section('5. Assign vehicles');

$response = request('/line/vehicles/' . $lineId, array(
    'submitVehicles' => 1,
    'VehicleIds'     => array($tramId),
));
check('A vehicle of another type is rejected',
      contains($response['body'], 'cannot run on a Autobus line'));

// 湊出 11 台 Autobus（本測試建立的 1 台 + 示範資料中的 10 台）
$busRows = db()->query('SELECT id FROM vehicle WHERE type = "Autobus" LIMIT 11');
$busIds  = array();
while ($row = $busRows->fetch_row()) {
    $busIds[] = (int)$row[0];
}
$response = request('/line/vehicles/' . $lineId, array(
    'submitVehicles' => 1,
    'VehicleIds'     => $busIds,
));
check('Selecting more than ten vehicles is rejected',
      contains($response['body'], 'maximum of 10 vehicles'), count($busIds) . ' selected');

$response = request('/line/vehicles/' . $lineId, array(
    'submitVehicles' => 1,
    'VehicleIds'     => array($vehicleId),
));
check('A free vehicle of the right type is accepted',
      $response['status'] === 302
      && (int)scalar('SELECT line_id FROM vehicle WHERE id = ' . $vehicleId) === $lineId);

// 已被本線占用的車輛，不會出現在其他線的可選清單中
$otherLineId = (int)scalar("SELECT id FROM `line` WHERE type = 'Autobus' AND id <> $lineId LIMIT 1");
if ($otherLineId > 0) {
    $response = request('/line/vehicles/' . $otherLineId);
    check('A vehicle already assigned is not offered to another line',
          !contains($response['body'], $vehicleName));
}

// ---------------------------------------------------------------------------
section('6. Line view shows map, stations and vehicles');

$response = request('/line/' . $lineId);
check('The route map is shown on the line view',
      contains($response['body'], 'uploads/maps/' . $mapFile));
check('All seven stations are listed', substr_count($response['body'], 'TEST Station ') === 7,
      substr_count($response['body'], 'TEST Station ') . ' stations shown');
check('The assigned vehicle is listed', contains($response['body'], $vehicleName));

// ---------------------------------------------------------------------------
section('7. Drivers');

$driverName = 'TEST DRIVER ' . strtoupper(substr(uniqid(), -6));
$avatarPath = dirname(__DIR__) . '/assets/images/driver.png';

$response = request('/driver/create', array(
    'Driver[name]'       => $driverName,
    'Driver[birth_date]' => '1990-04-01',
    'Driver[email]'      => 'test@lvb.de',
    'Driver[phone]'      => '0341-123456',
    'Driver[type]'       => 'Autobus',
    'Driver[vehicle_id]' => $vehicleId,
    'Driver[avatarFile]' => new CURLFile($avatarPath, 'image/png', 'driver.png'),
), true);

$driverId = (int)scalar("SELECT id FROM driver WHERE name = '" . db()->real_escape_string($driverName) . "'");
check('A driver was created and assigned to the vehicle',
      $driverId > 0 && (int)scalar('SELECT vehicle_id FROM driver WHERE id = ' . $driverId) === $vehicleId);

$avatarFile = scalar('SELECT avatar FROM driver WHERE id = ' . $driverId);
check('The uploaded avatar is stored on the server',
      $avatarFile !== 'avatar.png' && is_file(dirname(__DIR__) . '/uploads/avatars/' . $avatarFile),
      (string)$avatarFile);

$response = request('/driver/create', array(
    'Driver[name]'       => 'TEST BAD DRIVER',
    'Driver[birth_date]' => '1990-04-01',
    'Driver[email]'      => 'bad@lvb.de',
    'Driver[phone]'      => '0341-999',
    'Driver[type]'       => 'Tram',
    'Driver[vehicle_id]' => $vehicleId,
));
check('A driver cannot be assigned to a vehicle of another type',
      contains($response['body'], 'driver can only be assigned to a'));

$response = request('/driver/create', array(
    'Driver[name]'       => 'TEST BAD DATE',
    'Driver[birth_date]' => '1990-13-45',
    'Driver[email]'      => 'bad@lvb.de',
    'Driver[phone]'      => '0341-999',
    'Driver[type]'       => 'Autobus',
));
check('An invalid birth date is rejected',
      contains($response['body'], 'valid date') || contains($response['body'], 'existing date'));

$response = request('/driver/index');
check('The drivers report shows the new driver and its avatar',
      contains($response['body'], $driverName) && contains($response['body'], $avatarFile));

// ---------------------------------------------------------------------------
section('8. XML export');

$response = request('/xml/index');
check('The generated XML validates against lvb_system.xsd',
      contains($response['body'], 'valid against the XML Schema'));

$response = request('/xml/display');
check('The XML contains the new line (data comes from the database)',
      contains($response['body'], '<code>' . $lineCode . '</code>'));
check('The XML contains the assigned vehicle and its driver',
      contains($response['body'], '<name>' . $vehicleName . '</name>')
      && contains($response['body'], '<name>' . $driverName . '</name>'));
check('count_vehicles matches the number of vehicles',
      preg_match('#<code>' . preg_quote($lineCode, '#') . '</code>.*?<count_vehicles>1</count_vehicles>#s',
                 $response['body']) === 1);

// ---------------------------------------------------------------------------
section('9. Administrator users');

$login    = 'testadmin' . substr(uniqid(), -5);
$response = request('/user/create', array(
    'User[name]'        => 'Test Administrator',
    'User[gender]'      => 'F',
    'User[birth_date]'  => '1990-01-01',
    'User[email]'       => 'testadmin@lvb.de',
    'User[login]'       => $login,
    'User[newPassword]' => 'secret123',
));
$userId = (int)scalar("SELECT id FROM user WHERE login = '" . db()->real_escape_string($login) . "'");
check('A new administrator was created', $userId > 0, $login);
check('The password is stored as md5',
      scalar('SELECT password FROM user WHERE id = ' . $userId) === md5('secret123'));

// 以新帳號登入（另一個 cookie jar）
$mainJar   = $cookieJar;
$cookieJar = sys_get_temp_dir() . '/ws2013_module_e_cookies2.txt';
@unlink($cookieJar);
request('/site/login');
$response = request('/site/login', array(
    'LoginForm[username]' => $login,
    'LoginForm[password]' => 'secret123',
));
check('The new administrator can log in', $response['status'] === 302);
$cookieJar = $mainJar;

// ---------------------------------------------------------------------------
section('10. Update and delete');

$response = request('/line/update/' . $lineId, array(
    'Line[code]'                 => $lineCode,
    'Line[start_time_operation]' => '09:00:00',
    'Line[end_time_operation]'   => '17:00:00',
    'Line[type]'                 => 'Autobus',
));
check('The line can be modified',
      scalar('SELECT start_time_operation FROM `line` WHERE id = ' . $lineId) === '09:00:00');

request('/line/delete/' . $lineId);
check('The line was deleted', (int)scalar('SELECT COUNT(*) FROM `line` WHERE id = ' . $lineId) === 0);
check('Its stations are free again',
      (int)scalar('SELECT COUNT(*) FROM station WHERE line_id = ' . $lineId) === 0);
check('Its vehicles are free again',
      (int)scalar('SELECT line_id FROM vehicle WHERE id = ' . $vehicleId) === 0);

request('/vehicle/delete/' . $vehicleId);
check('Deleting a vehicle frees its drivers',
      (int)scalar('SELECT COUNT(*) FROM vehicle WHERE id = ' . $vehicleId) === 0
      && (int)scalar('SELECT vehicle_id FROM driver WHERE id = ' . $driverId) === 0);

// ---------------------------------------------------------------------------
section('11. Clean-up');

request('/driver/delete/' . $driverId);
request('/vehicle/delete/' . $tramId);
foreach ($createdStations as $stationId) {
    request('/station/delete/' . $stationId);
}
db()->query('DELETE FROM user WHERE id = ' . $userId);
db()->query("DELETE FROM driver WHERE name LIKE 'TEST %'");

check('All test records removed',
      (int)scalar("SELECT COUNT(*) FROM station WHERE name LIKE 'TEST Station%'") === 0
      && (int)scalar("SELECT COUNT(*) FROM vehicle WHERE name LIKE 'TEST-%'") === 0
      && (int)scalar("SELECT COUNT(*) FROM driver WHERE name LIKE 'TEST %'") === 0
      && (int)scalar("SELECT COUNT(*) FROM `line` WHERE code LIKE 'TEST %'") === 0);

// 清掉上傳的測試檔案
foreach (array('/uploads/maps/' . $mapFile, '/uploads/avatars/' . $avatarFile) as $file) {
    $path = dirname(__DIR__) . $file;
    if (is_file($path) && basename($path) !== 'avatar.png') {
        @unlink($path);
    }
}

// ---------------------------------------------------------------------------
echo PHP_EOL . str_repeat('-', 70) . PHP_EOL;
echo 'PASSED: ' . $passed . '   FAILED: ' . $failed . PHP_EOL;
exit($failed === 0 ? 0 : 1);
