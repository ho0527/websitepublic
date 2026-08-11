<?php
/**
 * D17 Simple Noticeboard
 *
 * 在同一頁完成「新增留言」與「顯示所有留言」，資料儲存於 log.json。
 * 完全不使用 JavaScript（表單直接 POST 回本頁，以 PHP 處理）。
 *
 * 儲存欄位：name（姓名）、content（內容）、date（發布時間 Y-m-d H:i:s）
 * 顯示順序：由新到舊
 * 送出後採用 POST/Redirect/GET，避免重新整理造成重複送出
 */

// PHP 預設時區為 UTC，明確設為本地時區，讓貼文時間與實際牆上時間一致
date_default_timezone_set('Asia/Taipei');

$logFile = __DIR__ . '/log.json';

/**
 * 讀取 log.json，回傳貼文陣列
 * 檔案不存在或內容為空（空檔）時一律視為空陣列
 *
 * @param string $file log.json 的路徑
 * @return array<int, array{name:string, content:string, date:string}>
 */
function loadPosts(string $file): array
{
    if (!is_file($file)) {
        return [];
    }

    $raw = trim((string) file_get_contents($file));

    if ($raw === '') {
        return [];
    }

    $posts = json_decode($raw, true);

    // JSON 損毀或格式不是陣列時，同樣視為空陣列，避免頁面出錯
    if (!is_array($posts)) {
        return [];
    }

    return $posts;
}

/**
 * 將貼文陣列寫回 log.json
 *
 * @param string $file  log.json 的路徑
 * @param array  $posts 貼文陣列
 */
function savePosts(string $file, array $posts): void
{
    file_put_contents(
        $file,
        json_encode($posts, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
    );
}

$errors = [];
$oldName = '';
$oldContent = '';

// ---------- 處理表單送出 ----------
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $oldName = trim((string) ($_POST['name'] ?? ''));
    $oldContent = trim((string) ($_POST['content'] ?? ''));

    if ($oldName === '') {
        $errors[] = '請輸入姓名（name）。';
    }

    if ($oldContent === '') {
        $errors[] = '請輸入內容（content）。';
    }

    if (empty($errors)) {
        $posts = loadPosts($logFile);

        // 新貼文附加在陣列尾端，顯示時再反轉為由新到舊
        $posts[] = [
            'name'    => $oldName,
            'content' => $oldContent,
            'date'    => date('Y-m-d H:i:s'),
        ];

        savePosts($logFile, $posts);

        // POST/Redirect/GET：導回本頁，避免重新整理重複送出
        header('Location: ' . $_SERVER['PHP_SELF'] . '?posted=1');
        exit;
    }
}

// ---------- 讀取要顯示的貼文（由新到舊） ----------
$posts = array_reverse(loadPosts($logFile));
$justPosted = isset($_GET['posted']);
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Noticeboard</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 32px 16px;
            background: #f3f5f9;
            color: #232830;
            font-family: "Segoe UI", "Microsoft JhengHei", Arial, sans-serif;
            line-height: 1.7;
        }

        .page {
            max-width: 720px;
            margin: 0 auto;
        }

        h1 {
            margin: 0 0 20px;
            font-size: 26px;
        }

        h2 {
            margin: 0 0 14px;
            font-size: 18px;
        }

        .card {
            background: #fff;
            border: 1px solid #dfe3ea;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 14px;
            border: 1px solid #c6ccd6;
            border-radius: 6px;
            font-family: inherit;
            font-size: 15px;
        }

        textarea {
            min-height: 110px;
            resize: vertical;
        }

        button {
            padding: 10px 20px;
            border: 0;
            border-radius: 6px;
            background: #2f6fed;
            color: #fff;
            font-size: 15px;
            cursor: pointer;
        }

        button:hover {
            background: #2559c4;
        }

        /* 錯誤訊息 */
        .errors {
            margin: 0 0 16px;
            padding: 12px 14px 12px 32px;
            background: #fdecea;
            border-left: 4px solid #d93025;
            border-radius: 4px;
            color: #a5251c;
        }

        /* 成功訊息 */
        .success {
            margin: 0 0 20px;
            padding: 12px 14px;
            background: #e9f7ef;
            border-left: 4px solid #1a7f4b;
            border-radius: 4px;
            color: #14663c;
        }

        /* 單則貼文 */
        .post {
            padding: 14px 0;
            border-bottom: 1px solid #e6e9ef;
        }

        .post:last-child {
            border-bottom: 0;
            padding-bottom: 0;
        }

        .post-head {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            gap: 12px;
            margin-bottom: 4px;
        }

        .post-name {
            font-weight: 700;
            color: #1c2733;
        }

        .post-date {
            font-size: 13px;
            color: #7a8494;
            white-space: nowrap;
        }

        .post-content {
            margin: 0;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .empty {
            color: #7a8494;
        }
    </style>
</head>
<body>

<div class="page">
    <h1>Noticeboard</h1>

    <?php if ($justPosted): ?>
        <p class="success">貼文已成功發布。</p>
    <?php endif; ?>

    <div class="card">
        <h2>新增貼文</h2>

        <?php if (!empty($errors)): ?>
            <ul class="errors">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') ?>">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" maxlength="50"
                   value="<?= htmlspecialchars($oldName, ENT_QUOTES, 'UTF-8') ?>">

            <label for="content">Content</label>
            <textarea id="content" name="content" maxlength="2000"><?= htmlspecialchars($oldContent, ENT_QUOTES, 'UTF-8') ?></textarea>

            <button type="submit">發布</button>
        </form>
    </div>

    <div class="card">
        <h2>所有貼文（<?= count($posts) ?> 則）</h2>

        <?php if (empty($posts)): ?>
            <p class="empty">目前還沒有任何貼文。</p>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <div class="post">
                    <div class="post-head">
                        <span class="post-name"><?= htmlspecialchars($post['name'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                        <span class="post-date"><?= htmlspecialchars($post['date'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <p class="post-content"><?= htmlspecialchars($post['content'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
