<?php
/**
 * Module E 安裝腳本。
 *
 * 1. 建立資料庫 ws2013_lvb
 * 2. 匯入題目提供的 material/Data/db_lvb.sql（由 latin1 轉為 UTF-8）
 * 3. 建立示範用的中間路線（依 Routes Intermediate Lines.docx 的站點順序）
 *
 * 使用方式：
 *   命令列  php setup/install.php
 *   瀏覽器  http://127.0.0.1:83/worldskill/2013/moduleE/setup/install.php
 */

$isCli = (PHP_SAPI === 'cli');
if (!$isCli) {
    header('Content-Type: text/plain; charset=UTF-8');
}

$moduleRoot = dirname(__DIR__);
$sqlFile    = $moduleRoot . '/material/Data/db_lvb.sql';

$dbHost = '127.0.0.1';
$dbPort = 3306;
$dbUser = 'root';
$dbPass = '';
$dbName = 'ws2013_lvb';

/** 輸出一行訊息 */
function out($message)
{
    echo $message . PHP_EOL;
}

// ---------------------------------------------------------------------------
// 1. 建立資料庫
// ---------------------------------------------------------------------------
$db = new mysqli($dbHost, $dbUser, $dbPass, null, $dbPort);
if ($db->connect_errno) {
    out('ERROR: cannot connect to MySQL - ' . $db->connect_error);
    exit(1);
}
$db->set_charset('utf8mb4');
$db->query('DROP DATABASE IF EXISTS `' . $dbName . '`');
$db->query('CREATE DATABASE `' . $dbName . '` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
$db->select_db($dbName);
out('[1/3] Database `' . $dbName . '` created.');

// ---------------------------------------------------------------------------
// 2. 匯入題目提供的 SQL
// ---------------------------------------------------------------------------
if (!is_file($sqlFile)) {
    out('ERROR: SQL dump not found at ' . $sqlFile);
    exit(1);
}
$sql = file_get_contents($sqlFile);
// 原始 dump 為 latin1（Windows-1252）編碼，轉成 UTF-8 才能正確顯示德文字母
$sql = mb_convert_encoding($sql, 'UTF-8', 'Windows-1252');
$sql = str_replace('DEFAULT CHARSET=latin1', 'DEFAULT CHARSET=utf8mb4', $sql);

if ($db->multi_query($sql)) {
    do {
        if ($result = $db->store_result()) {
            $result->free();
        }
    } while ($db->more_results() && $db->next_result());
}
if ($db->errno) {
    out('ERROR while importing: ' . $db->error);
    exit(1);
}
out('[2/3] material/Data/db_lvb.sql imported.');

// ---------------------------------------------------------------------------
// 3. 建立示範中間路線
// ---------------------------------------------------------------------------

// 站點順序取自 Routes Intermediate Lines.docx（起站、5 個中間站、終站）
$demoLines = array(
    array(
        'code' => 'Line T22', 'type' => 'Tram', 'start' => '06:00:00', 'end' => '23:00:00',
        'map' => 'Line_T22.svg',
        'stations' => array(8, 9, 10, 11, 12, 13, 14),
        'vehicles' => array(11, 12, 13),
    ),
    array(
        'code' => 'Line A33', 'type' => 'Autobus', 'start' => '08:00:00', 'end' => '16:00:00',
        'map' => 'Line_A33.svg',
        'stations' => array(15, 16, 17, 18, 19, 20, 21),
        'vehicles' => array(14, 15, 16, 17, 18, 19, 20, 21, 22, 23),
    ),
    array(
        'code' => 'Line A35', 'type' => 'Autobus', 'start' => '07:00:00', 'end' => '19:00:00',
        'map' => 'Line_A35.svg',
        'stations' => array(22, 23, 24, 25, 26, 27, 28),
        'vehicles' => array(24, 25),
    ),
    array(
        'code' => 'Line R87', 'type' => 'Regionalbus', 'start' => '06:00:00', 'end' => '20:00:00',
        'map' => 'Line_R87.svg',
        'stations' => array(29, 30, 31, 32, 33, 34, 35),
        'vehicles' => array(41, 42, 43, 44, 45, 46, 47, 48, 49, 50),
    ),
    array(
        'code' => 'Line N45', 'type' => 'Nightliner', 'start' => '18:00:00', 'end' => '07:00:00',
        'map' => 'Line_N45.svg',
        'stations' => array(36, 37, 38, 39, 40, 41, 42),
        'vehicles' => array(26, 27, 28, 29, 30, 31, 32, 33, 34, 35),
    ),
);

$positions = array('START', 'INTER', 'INTER', 'INTER', 'INTER', 'INTER', 'END');

foreach ($demoLines as $line) {
    $statement = $db->prepare(
        'INSERT INTO `line` (code, start_time_operation, end_time_operation, type, map) VALUES (?, ?, ?, ?, ?)');
    $statement->bind_param('sssss', $line['code'], $line['start'], $line['end'], $line['type'], $line['map']);
    $statement->execute();
    $lineId = $statement->insert_id;
    $statement->close();

    foreach ($line['stations'] as $index => $stationId) {
        $db->query('UPDATE `station` SET line_id = ' . (int)$lineId
                 . ", position_station = '" . $positions[$index] . "' WHERE id = " . (int)$stationId);
    }
    foreach ($line['vehicles'] as $vehicleId) {
        $db->query('UPDATE `vehicle` SET line_id = ' . (int)$lineId . ' WHERE id = ' . (int)$vehicleId);
    }

    out('       created ' . $line['code'] . ' (' . count($line['stations']) . ' stations, '
        . count($line['vehicles']) . ' vehicles)');
}

// 讓每條路線的第一台車都有司機，方便展示 XML 中的 driver 節點
$db->query("UPDATE `driver` d
            JOIN `vehicle` v ON v.type = d.type AND v.line_id > 0
            SET d.vehicle_id = v.id
            WHERE d.vehicle_id = 0
              AND v.id = (SELECT MIN(id) FROM (SELECT * FROM `vehicle`) x
                          WHERE x.type = d.type AND x.line_id > 0)
            LIMIT 4");

out('[3/3] Demo Intermediate Lines created.');
out('');
out('Done. Log in at http://127.0.0.1:83/worldskill/2013/moduleE/ with webmaster / leipzig');
