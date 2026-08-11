<?php
/**
 * 模組 C - 停車場 API
 * GET api/parking.php          取得全部停車場
 * GET api/parking.php?id=1     取得單一停車場（詳情頁每 10 秒輪詢）
 */

declare(strict_types=1);

require __DIR__ . '/db.php';

try {
    $pdo = db();
    $columns = 'id, name, address, total_spaces, available_spaces,
                latitude, longitude, updated_at';

    if (isset($_GET['id'])) {
        $statement = $pdo->prepare("SELECT {$columns} FROM parking_lots WHERE id = ?");
        $statement->execute([(int) $_GET['id']]);
        $row = $statement->fetch();

        if ($row === false) {
            jsonError('找不到指定的停車場', 404);
        }

        jsonResponse(['data' => normalizeParkingLot($row)]);
    }

    $rows = $pdo->query("SELECT {$columns} FROM parking_lots ORDER BY name")->fetchAll();

    jsonResponse([
        'data'       => array_map('normalizeParkingLot', $rows),
        'fetched_at' => date('c'),
    ]);
} catch (Throwable $error) {
    jsonError('停車場資料讀取失敗：' . $error->getMessage(), 500);
}

/**
 * 將資料庫欄位轉為前端使用的型別。
 */
function normalizeParkingLot(array $row): array
{
    return [
        'id'         => (int) $row['id'],
        'name'       => $row['name'],
        'address'    => $row['address'],
        'total'      => (int) $row['total_spaces'],
        'available'  => (int) $row['available_spaces'],
        'latitude'   => (float) $row['latitude'],
        'longitude'  => (float) $row['longitude'],
        'updated_at' => $row['updated_at'],
    ];
}
