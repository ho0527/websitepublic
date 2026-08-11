<?php
/**
 * 排行榜 API
 *
 * POST /api/ranking.php  { name, difficult_id, seconds }
 *   → 寫入一筆成績並回傳該難度的排行資料
 * GET  /api/ranking.php?difficult_id=1[&id=12]
 *   → 只查詢排行資料
 *
 * 回傳格式：
 * {
 *   ok: true,
 *   level: "EASY",
 *   rows: [ { position, level, name, time, seconds, me } ],   // 顯示用（前三名 + 目前玩家）
 *   me:   { position, level, name, time, seconds },
 *   meInTop: bool
 * }
 */

declare(strict_types=1);

require __DIR__ . '/db.php';

try {
    $pdo    = db();
    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'POST') {
        $raw   = file_get_contents('php://input');
        $input = json_decode($raw, true);
        if (!is_array($input)) {
            $input = $_POST;
        }

        $name        = trim((string) ($input['name'] ?? ''));
        $difficultId = (int) ($input['difficult_id'] ?? 0);
        $seconds     = (int) round((float) ($input['seconds'] ?? -1));

        // 後端驗證：三個欄位都必須合法
        if ($name === '' || mb_strlen($name) > 155) {
            jsonResponse(['ok' => false, 'error' => 'Invalid name.'], 422);
        }
        if (!in_array($difficultId, [1, 2, 3], true)) {
            jsonResponse(['ok' => false, 'error' => 'Invalid difficulty level.'], 422);
        }
        if ($seconds < 0 || $seconds > 359999) {
            jsonResponse(['ok' => false, 'error' => 'Invalid elapsed time.'], 422);
        }

        $stmt = $pdo->prepare(
            'INSERT INTO `ranking` (`name`, `difficult_id`, `time`) VALUES (:name, :difficult_id, SEC_TO_TIME(:seconds))'
        );
        $stmt->execute([
            ':name'         => $name,
            ':difficult_id' => $difficultId,
            ':seconds'      => $seconds,
        ]);

        $currentId = (int) $pdo->lastInsertId();
    } else {
        $difficultId = (int) ($_GET['difficult_id'] ?? 0);
        $currentId   = (int) ($_GET['id'] ?? 0);
        if (!in_array($difficultId, [1, 2, 3], true)) {
            jsonResponse(['ok' => false, 'error' => 'Invalid difficulty level.'], 422);
        }
    }

    jsonResponse(buildRanking($pdo, $difficultId, $currentId));
} catch (Throwable $e) {
    jsonResponse(['ok' => false, 'error' => 'Server error: ' . $e->getMessage()], 500);
}

/**
 * 產生排行資料：同一難度、依耗時由小到大，並用「同時間同名次」的方式編號。
 */
function buildRanking(PDO $pdo, int $difficultId, int $currentId): array
{
    $stmt = $pdo->prepare(
        'SELECT r.`id`, r.`name`, d.`name` AS level, TIME_TO_SEC(r.`time`) AS seconds
           FROM `ranking` r
           JOIN `difficult` d ON d.`id` = r.`difficult_id`
          WHERE r.`difficult_id` = :difficult_id
          ORDER BY seconds ASC, r.`id` ASC'
    );
    $stmt->execute([':difficult_id' => $difficultId]);
    $all = $stmt->fetchAll();

    // 依耗時計算名次（並列同名次）
    $position  = 0;
    $lastValue = null;
    foreach ($all as $i => &$row) {
        $row['seconds'] = (int) $row['seconds'];
        if ($lastValue === null || $row['seconds'] !== $lastValue) {
            $position  = $i + 1;
            $lastValue = $row['seconds'];
        }
        $row['position'] = $position;
        $row['time']     = formatTime($row['seconds']);
        $row['me']       = ((int) $row['id'] === $currentId && $currentId > 0);
    }
    unset($row);

    // 前三名（依名次而非筆數，故並列第三會一起顯示）
    $top = array_values(array_filter($all, static fn(array $r): bool => $r['position'] <= 3));

    $me      = null;
    $meInTop = false;
    foreach ($all as $row) {
        if ($row['me']) {
            $me      = $row;
            $meInTop = $row['position'] <= 3;
            break;
        }
    }

    $rows = $top;
    if ($me !== null && !$meInTop) {
        $rows[] = $me;
    }

    $level = $all[0]['level'] ?? levelName($difficultId);

    return [
        'ok'      => true,
        'level'   => $level,
        'rows'    => $rows,
        'me'      => $me,
        'meInTop' => $meInTop,
        'total'   => count($all),
    ];
}

/** 秒數轉 mm:ss（超過 60 分鐘時仍以總分鐘數呈現） */
function formatTime(int $seconds): string
{
    return sprintf('%02d:%02d', intdiv($seconds, 60), $seconds % 60);
}

function levelName(int $difficultId): string
{
    return [1 => 'EASY', 2 => 'MEDIUM', 3 => 'HARD'][$difficultId] ?? 'EASY';
}
