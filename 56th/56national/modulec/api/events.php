<?php
/**
 * 模組 C - 活動 API（支援日期篩選與無限捲動分頁）
 * GET api/events.php?start=YYYY-MM-DD&end=YYYY-MM-DD&offset=0&limit=8
 * 回傳 data、total、offset、limit、has_more，前端據此判斷是否還要載入下一頁。
 */

declare(strict_types=1);

require __DIR__ . '/db.php';

try {
    $pdo = db();

    $start = queryDate('start');
    $end = queryDate('end');
    $offset = queryInt('offset', 0, 0, 100000);
    $limit = queryInt('limit', 8, 1, 50);

    // 篩選條件：活動期間與使用者選定的區間有重疊即符合
    $conditions = [];
    $params = [];

    if ($start !== null) {
        $conditions[] = 'end_date >= ?';
        $params[] = $start;
    }
    if ($end !== null) {
        $conditions[] = 'start_date <= ?';
        $params[] = $end;
    }

    $where = $conditions === [] ? '' : ' WHERE ' . implode(' AND ', $conditions);

    $countStatement = $pdo->prepare('SELECT COUNT(*) FROM events' . $where);
    $countStatement->execute($params);
    $total = (int) $countStatement->fetchColumn();

    // 以 start_date + id 排序確保分頁穩定，不會重複或遺漏
    $listStatement = $pdo->prepare(
        'SELECT id, title, description, start_date, end_date, image_color, image_url
         FROM events' . $where . '
         ORDER BY start_date ASC, id ASC
         LIMIT ' . $limit . ' OFFSET ' . $offset
    );
    $listStatement->execute($params);
    $rows = $listStatement->fetchAll();

    $data = array_map(static function (array $row): array {
        return [
            'id'          => (int) $row['id'],
            'title'       => $row['title'],
            'description' => $row['description'],
            'start_date'  => $row['start_date'],
            'end_date'    => $row['end_date'],
            'image_color' => $row['image_color'],
            'image_url'   => $row['image_url'],
        ];
    }, $rows);

    jsonResponse([
        'data'     => $data,
        'total'    => $total,
        'offset'   => $offset,
        'limit'    => $limit,
        'has_more' => ($offset + count($data)) < $total,
    ]);
} catch (Throwable $error) {
    jsonError('活動資料讀取失敗：' . $error->getMessage(), 500);
}
