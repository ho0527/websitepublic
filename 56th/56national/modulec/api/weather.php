<?php
/**
 * 模組 C - 天氣 API
 * GET api/weather.php  取得自今日起連續 7 天的天氣預報。
 */

declare(strict_types=1);

require __DIR__ . '/db.php';

try {
    $pdo = db();

    $statement = $pdo->prepare(
        'SELECT forecast_date, condition_text, icon, high_temp, low_temp, rain_chance
         FROM weather
         WHERE forecast_date >= CURDATE()
         ORDER BY forecast_date ASC
         LIMIT 7'
    );
    $statement->execute();
    $rows = $statement->fetchAll();

    // 若今日之後的資料不足 7 天，改回傳資料表中最新的 7 天，避免畫面空白
    if (count($rows) < 7) {
        $rows = $pdo->query(
            'SELECT forecast_date, condition_text, icon, high_temp, low_temp, rain_chance
             FROM weather
             ORDER BY forecast_date DESC
             LIMIT 7'
        )->fetchAll();
        $rows = array_reverse($rows);
    }

    $data = array_map(static function (array $row): array {
        return [
            'date'        => $row['forecast_date'],
            'condition'   => $row['condition_text'],
            'icon'        => $row['icon'],
            'high'        => (int) $row['high_temp'],
            'low'         => (int) $row['low_temp'],
            'rain_chance' => (int) $row['rain_chance'],
        ];
    }, $rows);

    jsonResponse(['data' => $data]);
} catch (Throwable $error) {
    jsonError('天氣資料讀取失敗：' . $error->getMessage(), 500);
}
