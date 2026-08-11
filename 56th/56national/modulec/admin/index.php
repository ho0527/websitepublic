<?php
/**
 * 模組 C - 後台管理
 * 提供停車場、活動、天氣三張資料表的新增／修改／刪除，以及各自的特殊功能。
 * 網址：admin/index.php?table=parking|events|weather
 */

declare(strict_types=1);

require __DIR__ . '/../api/db.php';

/** 可管理的資料表設定 */
const TABLES = [
    'parking' => ['label' => '停車場', 'table' => 'parking_lots'],
    'events'  => ['label' => '活動',   'table' => 'events'],
    'weather' => ['label' => '天氣',   'table' => 'weather'],
];

$table = isset($_GET['table']) && isset(TABLES[$_GET['table']]) ? (string) $_GET['table'] : 'parking';
$message = '';
$messageType = 'info';

/** HTML 逸出的簡寫 */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

try {
    $pdo = db();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = (string) ($_POST['action'] ?? '');
        $id = (int) ($_POST['id'] ?? 0);

        switch ($action) {
            // ---------- 停車場 ----------
            case 'parking_save':
                $fields = [
                    trim((string) $_POST['name']),
                    trim((string) $_POST['address']),
                    (int) $_POST['total_spaces'],
                    (int) $_POST['available_spaces'],
                    (float) $_POST['latitude'],
                    (float) $_POST['longitude'],
                ];
                if ($id > 0) {
                    $statement = $pdo->prepare(
                        'UPDATE parking_lots SET name = ?, address = ?, total_spaces = ?,
                         available_spaces = ?, latitude = ?, longitude = ? WHERE id = ?'
                    );
                    $statement->execute([...$fields, $id]);
                    $message = '已更新停車場資料';
                } else {
                    $statement = $pdo->prepare(
                        'INSERT INTO parking_lots (name, address, total_spaces, available_spaces, latitude, longitude)
                         VALUES (?, ?, ?, ?, ?, ?)'
                    );
                    $statement->execute($fields);
                    $message = '已新增停車場';
                }
                break;

            case 'parking_delete':
                $pdo->prepare('DELETE FROM parking_lots WHERE id = ?')->execute([$id]);
                $message = '已刪除停車場';
                break;

            case 'parking_fill':
                // 特殊功能：一鍵將所有停車場設為滿場（可用車位歸零）
                $pdo->exec('UPDATE parking_lots SET available_spaces = 0');
                $message = '已將所有停車場設為滿場';
                break;

            case 'parking_simulate':
                // 特殊功能：隨機模擬即時車位變化，用於驗證詳情頁 10 秒自動更新
                $pdo->exec(
                    'UPDATE parking_lots
                     SET available_spaces = LEAST(total_spaces, GREATEST(0, FLOOR(RAND() * total_spaces)))'
                );
                $message = '已隨機模擬各停車場的即時空位';
                break;

            // ---------- 活動 ----------
            case 'events_save':
                $fields = [
                    trim((string) $_POST['title']),
                    trim((string) $_POST['description']),
                    (string) $_POST['start_date'],
                    (string) $_POST['end_date'],
                    (string) $_POST['image_color'],
                    trim((string) $_POST['image_url']),
                ];
                if ($id > 0) {
                    $statement = $pdo->prepare(
                        'UPDATE events SET title = ?, description = ?, start_date = ?,
                         end_date = ?, image_color = ?, image_url = ? WHERE id = ?'
                    );
                    $statement->execute([...$fields, $id]);
                    $message = '已更新活動';
                } else {
                    $statement = $pdo->prepare(
                        'INSERT INTO events (title, description, start_date, end_date, image_color, image_url)
                         VALUES (?, ?, ?, ?, ?, ?)'
                    );
                    $statement->execute($fields);
                    $message = '已新增活動';
                }
                break;

            case 'events_delete':
                $pdo->prepare('DELETE FROM events WHERE id = ?')->execute([$id]);
                $message = '已刪除活動';
                break;

            case 'events_duplicate':
                // 特殊功能：複製活動並自動順延一年，方便建立年度例行展覽
                $statement = $pdo->prepare(
                    'INSERT INTO events (title, description, start_date, end_date, image_color, image_url)
                     SELECT title, description,
                            DATE_ADD(start_date, INTERVAL 1 YEAR),
                            DATE_ADD(end_date, INTERVAL 1 YEAR),
                            image_color, image_url
                     FROM events WHERE id = ?'
                );
                $statement->execute([$id]);
                $message = '已複製活動並順延一年';
                break;

            case 'events_purge':
                // 特殊功能：批次清除已結束的活動
                $statement = $pdo->prepare('DELETE FROM events WHERE end_date < CURDATE()');
                $statement->execute();
                $message = '已清除 ' . $statement->rowCount() . ' 筆過期活動';
                break;

            // ---------- 天氣 ----------
            case 'weather_save':
                $fields = [
                    (string) $_POST['forecast_date'],
                    trim((string) $_POST['condition_text']),
                    (string) $_POST['icon'],
                    (int) $_POST['high_temp'],
                    (int) $_POST['low_temp'],
                    (int) $_POST['rain_chance'],
                ];
                if ($id > 0) {
                    $statement = $pdo->prepare(
                        'UPDATE weather SET forecast_date = ?, condition_text = ?, icon = ?,
                         high_temp = ?, low_temp = ?, rain_chance = ? WHERE id = ?'
                    );
                    $statement->execute([...$fields, $id]);
                    $message = '已更新天氣';
                } else {
                    $statement = $pdo->prepare(
                        'INSERT INTO weather (forecast_date, condition_text, icon, high_temp, low_temp, rain_chance)
                         VALUES (?, ?, ?, ?, ?, ?)
                         ON DUPLICATE KEY UPDATE condition_text = VALUES(condition_text), icon = VALUES(icon),
                             high_temp = VALUES(high_temp), low_temp = VALUES(low_temp),
                             rain_chance = VALUES(rain_chance)'
                    );
                    $statement->execute($fields);
                    $message = '已新增天氣資料';
                }
                break;

            case 'weather_delete':
                $pdo->prepare('DELETE FROM weather WHERE id = ?')->execute([$id]);
                $message = '已刪除天氣資料';
                break;

            case 'weather_shift':
                // 特殊功能：把資料表中最早的一天平移到今天，讓前端永遠有未來 7 天可顯示
                $earliest = $pdo->query('SELECT MIN(forecast_date) FROM weather')->fetchColumn();
                if ($earliest) {
                    $days = (int) $pdo->query(
                        'SELECT DATEDIFF(CURDATE(), ' . $pdo->quote((string) $earliest) . ')'
                    )->fetchColumn();
                    $pdo->exec('UPDATE weather SET forecast_date = DATE_ADD(forecast_date, INTERVAL ' . $days . ' DAY)');
                    $message = '已將天氣預報平移 ' . $days . ' 天至今日起算';
                }
                break;
        }

        $messageType = 'ok';
    }

    // 讀取列表資料
    $rows = match ($table) {
        'parking' => $pdo->query('SELECT * FROM parking_lots ORDER BY name')->fetchAll(),
        'events'  => $pdo->query('SELECT * FROM events ORDER BY start_date DESC, id DESC LIMIT 200')->fetchAll(),
        'weather' => $pdo->query('SELECT * FROM weather ORDER BY forecast_date ASC LIMIT 200')->fetchAll(),
    };

    // 編輯中的單筆資料
    $editing = null;
    if (isset($_GET['edit'])) {
        $editId = (int) $_GET['edit'];
        foreach ($rows as $row) {
            if ((int) $row['id'] === $editId) {
                $editing = $row;
                break;
            }
        }
    }
} catch (Throwable $error) {
    $rows = [];
    $editing = null;
    $message = '資料庫錯誤：' . $error->getMessage() . '（請先執行 ../api/install.php）';
    $messageType = 'error';
}
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>模組 C 後台管理</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <header class="admin-header">
        <h1>南港展覽館服務 － 後台管理</h1>
        <nav class="admin-nav">
            <?php foreach (TABLES as $key => $config): ?>
                <a class="<?= $key === $table ? 'active' : '' ?>" href="?table=<?= e($key) ?>"><?= e($config['label']) ?></a>
            <?php endforeach; ?>
            <a href="../">回前台</a>
            <a href="../api/install.php">重建資料庫</a>
        </nav>
    </header>

    <main class="admin-main">
        <?php if ($message !== ''): ?>
            <p class="flash <?= e($messageType) ?>"><?= e($message) ?></p>
        <?php endif; ?>

        <?php if ($table === 'parking'): ?>
            <section class="panel">
                <h2><?= $editing ? '編輯停車場' : '新增停車場' ?></h2>
                <form method="post" class="form-grid">
                    <input type="hidden" name="action" value="parking_save">
                    <input type="hidden" name="id" value="<?= e($editing['id'] ?? 0) ?>">
                    <label>名稱<input name="name" required value="<?= e($editing['name'] ?? '') ?>"></label>
                    <label>地址<input name="address" value="<?= e($editing['address'] ?? '') ?>"></label>
                    <label>總車位<input name="total_spaces" type="number" min="0" required value="<?= e($editing['total_spaces'] ?? 100) ?>"></label>
                    <label>可用車位<input name="available_spaces" type="number" min="0" required value="<?= e($editing['available_spaces'] ?? 0) ?>"></label>
                    <label>緯度<input name="latitude" type="number" step="0.0000001" required value="<?= e($editing['latitude'] ?? '25.0561') ?>"></label>
                    <label>經度<input name="longitude" type="number" step="0.0000001" required value="<?= e($editing['longitude'] ?? '121.6178') ?>"></label>
                    <div class="form-actions">
                        <button type="submit">儲存</button>
                        <?php if ($editing): ?><a class="button-link" href="?table=parking">取消編輯</a><?php endif; ?>
                    </div>
                </form>
            </section>

            <section class="panel">
                <h2>特殊功能</h2>
                <form method="post" class="inline-form">
                    <button type="submit" name="action" value="parking_simulate">隨機模擬即時空位</button>
                    <button type="submit" name="action" value="parking_fill" class="danger">全部設為滿場</button>
                </form>
            </section>

            <table class="data-table">
                <thead>
                    <tr><th>名稱</th><th>可用／總數</th><th>座標</th><th>更新時間</th><th>操作</th></tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><?= e($row['name']) ?><br><small><?= e($row['address']) ?></small></td>
                        <td><?= e($row['available_spaces']) ?> / <?= e($row['total_spaces']) ?></td>
                        <td><?= e($row['latitude']) ?>, <?= e($row['longitude']) ?></td>
                        <td><?= e($row['updated_at']) ?></td>
                        <td class="row-actions">
                            <a href="?table=parking&edit=<?= e($row['id']) ?>">編輯</a>
                            <form method="post" onsubmit="return confirm('確定刪除？');">
                                <input type="hidden" name="id" value="<?= e($row['id']) ?>">
                                <button type="submit" name="action" value="parking_delete" class="danger">刪除</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

        <?php elseif ($table === 'events'): ?>
            <section class="panel">
                <h2><?= $editing ? '編輯活動' : '新增活動' ?></h2>
                <form method="post" class="form-grid">
                    <input type="hidden" name="action" value="events_save">
                    <input type="hidden" name="id" value="<?= e($editing['id'] ?? 0) ?>">
                    <label>活動名稱<input name="title" required value="<?= e($editing['title'] ?? '') ?>"></label>
                    <label>說明<input name="description" value="<?= e($editing['description'] ?? '') ?>"></label>
                    <label>開始日期<input name="start_date" type="date" required value="<?= e($editing['start_date'] ?? date('Y-m-d')) ?>"></label>
                    <label>結束日期<input name="end_date" type="date" required value="<?= e($editing['end_date'] ?? date('Y-m-d')) ?>"></label>
                    <label>主題色<input name="image_color" type="color" value="<?= e($editing['image_color'] ?? '#1c3e60') ?>"></label>
                    <label>圖片網址（可留空）<input name="image_url" value="<?= e($editing['image_url'] ?? '') ?>"></label>
                    <div class="form-actions">
                        <button type="submit">儲存</button>
                        <?php if ($editing): ?><a class="button-link" href="?table=events">取消編輯</a><?php endif; ?>
                    </div>
                </form>
            </section>

            <section class="panel">
                <h2>特殊功能</h2>
                <form method="post" class="inline-form" onsubmit="return confirm('確定清除所有已結束的活動？');">
                    <button type="submit" name="action" value="events_purge" class="danger">清除過期活動</button>
                </form>
            </section>

            <table class="data-table">
                <thead>
                    <tr><th>活動</th><th>期間</th><th>色票</th><th>操作</th></tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><?= e($row['title']) ?><br><small><?= e($row['description']) ?></small></td>
                        <td><?= e($row['start_date']) ?> ~ <?= e($row['end_date']) ?></td>
                        <td><span class="swatch" style="background: <?= e($row['image_color']) ?>"></span></td>
                        <td class="row-actions">
                            <a href="?table=events&edit=<?= e($row['id']) ?>">編輯</a>
                            <form method="post">
                                <input type="hidden" name="id" value="<?= e($row['id']) ?>">
                                <button type="submit" name="action" value="events_duplicate">複製順延一年</button>
                            </form>
                            <form method="post" onsubmit="return confirm('確定刪除？');">
                                <input type="hidden" name="id" value="<?= e($row['id']) ?>">
                                <button type="submit" name="action" value="events_delete" class="danger">刪除</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

        <?php else: ?>
            <section class="panel">
                <h2><?= $editing ? '編輯天氣' : '新增天氣' ?></h2>
                <form method="post" class="form-grid">
                    <input type="hidden" name="action" value="weather_save">
                    <input type="hidden" name="id" value="<?= e($editing['id'] ?? 0) ?>">
                    <label>日期<input name="forecast_date" type="date" required value="<?= e($editing['forecast_date'] ?? date('Y-m-d')) ?>"></label>
                    <label>天氣描述<input name="condition_text" required value="<?= e($editing['condition_text'] ?? '') ?>"></label>
                    <label>圖示
                        <select name="icon">
                            <?php foreach (['sunny' => '晴', 'cloudy' => '多雲', 'rainy' => '雨'] as $value => $text): ?>
                                <option value="<?= e($value) ?>" <?= ($editing['icon'] ?? '') === $value ? 'selected' : '' ?>><?= e($text) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label>高溫<input name="high_temp" type="number" required value="<?= e($editing['high_temp'] ?? 30) ?>"></label>
                    <label>低溫<input name="low_temp" type="number" required value="<?= e($editing['low_temp'] ?? 24) ?>"></label>
                    <label>降雨機率<input name="rain_chance" type="number" min="0" max="100" required value="<?= e($editing['rain_chance'] ?? 20) ?>"></label>
                    <div class="form-actions">
                        <button type="submit">儲存</button>
                        <?php if ($editing): ?><a class="button-link" href="?table=weather">取消編輯</a><?php endif; ?>
                    </div>
                </form>
            </section>

            <section class="panel">
                <h2>特殊功能</h2>
                <form method="post" class="inline-form">
                    <button type="submit" name="action" value="weather_shift">將預報平移至今日起算</button>
                </form>
            </section>

            <table class="data-table">
                <thead>
                    <tr><th>日期</th><th>天氣</th><th>圖示</th><th>高／低溫</th><th>降雨</th><th>操作</th></tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><?= e($row['forecast_date']) ?></td>
                        <td><?= e($row['condition_text']) ?></td>
                        <td><?= e($row['icon']) ?></td>
                        <td><?= e($row['high_temp']) ?>° / <?= e($row['low_temp']) ?>°</td>
                        <td><?= e($row['rain_chance']) ?>%</td>
                        <td class="row-actions">
                            <a href="?table=weather&edit=<?= e($row['id']) ?>">編輯</a>
                            <form method="post" onsubmit="return confirm('確定刪除？');">
                                <input type="hidden" name="id" value="<?= e($row['id']) ?>">
                                <button type="submit" name="action" value="weather_delete" class="danger">刪除</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </main>
</body>
</html>
