<?php
/**
 * 建立／還原資料庫
 *
 * 使用方式：
 *   1. 瀏覽器開啟 http://127.0.0.1:83/53th/senior/53grandmaster2stage/TaskA/setup.php
 *   2. 或於命令列執行 php setup.php
 *
 * 執行後會重建 users、books、rents 三張資料表，並寫入試題指定的內建帳號與範例資料。
 */

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

$isCli = PHP_SAPI === 'cli';

try {
    Installer::install();

    $users = Database::select('SELECT id, email, username, role FROM users ORDER BY id');
    $books = Database::select('SELECT id, name, isbn FROM books ORDER BY id');
    $rents = Database::select('SELECT id, user_id, book_id FROM rents ORDER BY id');

    if ($isCli) {
        echo "資料庫建立完成：" . Database::databaseName() . PHP_EOL;
        echo "使用者 " . count($users) . " 筆、書籍 " . count($books) . " 筆、租借 " . count($rents) . " 筆" . PHP_EOL;
        exit(0);
    }
} catch (Throwable $error) {
    if ($isCli) {
        fwrite(STDERR, '安裝失敗：' . $error->getMessage() . PHP_EOL);
        exit(1);
    }
    http_response_code(500);
    $failure = $error->getMessage();
}

/** 輸出跳脫，避免 XSS */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>模組 A - 資料庫安裝</title>
    <style>
        body { margin: 0; padding: 24px; background: #10141c; color: #e6ebf5; font-family: "Segoe UI", "Microsoft JhengHei", sans-serif; }
        h1 { font-size: 20px; }
        table { border-collapse: collapse; margin-bottom: 24px; width: 100%; max-width: 900px; }
        caption { text-align: left; font-weight: 700; color: #f19e0d; padding: 8px 0; }
        th, td { border: 1px solid #2c3648; padding: 6px 10px; font-size: 14px; text-align: left; }
        th { background: #1b2230; }
        .ok { color: #7ddc7d; }
        .fail { color: #ff8080; }
        a { color: #f19e0d; }
    </style>
</head>
<body>
    <h1>模組 A - 資料庫安裝</h1>

    <?php if (isset($failure)): ?>
        <p class="fail">安裝失敗：<?= e($failure) ?></p>
    <?php else: ?>
        <p class="ok">資料庫 <strong><?= e(Database::databaseName()) ?></strong> 建立完成。</p>

        <table>
            <caption>users</caption>
            <tr><th>id</th><th>email</th><th>username</th><th>role</th></tr>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= e($user['id']) ?></td>
                    <td><?= e($user['email']) ?></td>
                    <td><?= e($user['username']) ?></td>
                    <td><?= e($user['role']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <table>
            <caption>books</caption>
            <tr><th>id</th><th>name</th><th>isbn</th></tr>
            <?php foreach ($books as $book): ?>
                <tr>
                    <td><?= e($book['id']) ?></td>
                    <td><?= e($book['name']) ?></td>
                    <td><?= e($book['isbn']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <table>
            <caption>rents</caption>
            <tr><th>id</th><th>user_id</th><th>book_id</th></tr>
            <?php foreach ($rents as $rent): ?>
                <tr>
                    <td><?= e($rent['id']) ?></td>
                    <td><?= e($rent['user_id']) ?></td>
                    <td><?= e($rent['book_id']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <p><a href="./">前往 GraphQL 查詢主控台</a></p>
    <?php endif; ?>
</body>
</html>
