<?php
/**
 * Star Battle - 成績登錄（伺服器端）
 *
 * 由前端以 AJAX（POST）呼叫，參數：
 *   name  玩家名稱
 *   time  飛行秒數（整數）
 *   score 得分（整數，可為負）
 *
 * 回傳：資料庫中所有成績的 JSON 陣列（未排序，排序由前端負責）
 *   [ {"id":"1","name":"Player 1","time":"20","score":"10"}, ... ]
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

/* ---------- 資料庫連線設定（本機 MySQL：root / 空密碼） ---------- */
$host     = '127.0.0.1';
$port     = 3306;
$dbname   = 's53g2_starbattle';
$username = 'root';
$password = '';

try {
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    // 第一次執行時自動建立資料庫與資料表（等同匯入 ranking.sql）
    $server = new PDO("mysql:host={$host};port={$port};charset=utf8mb4", $username, $password, $options);
    $server->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4", $username, $password, $options);
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS ranking (
            id    INT UNSIGNED NOT NULL AUTO_INCREMENT,
            name  VARCHAR(50) NOT NULL,
            time  INT NOT NULL DEFAULT 0,
            score INT NOT NULL DEFAULT 0,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );

    // 有帶參數時才新增紀錄，否則單純回傳目前的排行榜
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && isset($_POST['name'])) {
        $name  = trim((string) $_POST['name']);
        $time  = (int) ($_POST['time'] ?? 0);
        $score = (int) ($_POST['score'] ?? 0);

        // 名稱長度限制，避免異常資料
        if ($name === '') {
            $name = 'Anonymous';
        }
        $name = mb_substr($name, 0, 50);

        // 使用 prepared statement，防止 SQL Injection
        $statement = $pdo->prepare('INSERT INTO ranking (name, time, score) VALUES (:name, :time, :score)');
        $statement->execute([
            ':name'  => $name,
            ':time'  => $time,
            ':score' => $score,
        ]);
    }

    // 依試題說明回傳未排序的資料，欄位皆為字串型別
    $rows = $pdo->query('SELECT id, name, time, score FROM ranking')->fetchAll();

    $result = array_map(static function (array $row): array {
        return [
            'id'    => (string) $row['id'],
            'name'  => (string) $row['name'],
            'time'  => (string) $row['time'],
            'score' => (string) $row['score'],
        ];
    }, $rows);

    echo json_encode($result, JSON_UNESCAPED_UNICODE);
} catch (Throwable $error) {
    http_response_code(500);
    echo json_encode(['error' => $error->getMessage()], JSON_UNESCAPED_UNICODE);
}
