<?php
/**
 * D16 API Request Logger - 說明頁
 * 顯示 API 的呼叫方式，以及目前 logs/ 資料夾內已記錄的檔案列表與內容預覽。
 */

// PHP 預設時區為 UTC，明確設為本地時區，與 api.php 一致
date_default_timezone_set('Asia/Taipei');

$logDirectory = __DIR__ . '/logs';

// 確保 logs 資料夾存在（與 api.php 相同的行為）
if (!is_dir($logDirectory)) {
    @mkdir($logDirectory, 0777, true);
}

// 取得所有紀錄檔，依修改時間由新到舊排序
$logFiles = glob($logDirectory . '/*-request*.txt') ?: [];
usort($logFiles, static function (string $a, string $b): int {
    return filemtime($b) <=> filemtime($a);
});

// 目前頁面所在目錄的網址（用來組出 API 的完整網址）
$baseUrl = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
$apiUrl = 'http://' . ($_SERVER['HTTP_HOST'] ?? '127.0.0.1') . $baseUrl . '/api.php';
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Request Logger</title>
    <style>
        body {
            margin: 0;
            padding: 32px 16px;
            background: #f4f6f9;
            color: #232830;
            font-family: "Segoe UI", "Microsoft JhengHei", Arial, sans-serif;
            line-height: 1.7;
        }

        .page {
            max-width: 860px;
            margin: 0 auto;
        }

        h1 {
            margin: 0 0 8px;
            font-size: 26px;
        }

        h2 {
            margin: 0 0 12px;
            font-size: 18px;
        }

        .card {
            background: #fff;
            border: 1px solid #dfe3ea;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }

        code,
        pre {
            font-family: Consolas, "Courier New", monospace;
            font-size: 13px;
        }

        pre {
            margin: 0;
            padding: 12px 14px;
            background: #1e232b;
            color: #e6e9ef;
            border-radius: 6px;
            overflow-x: auto;
            white-space: pre-wrap;
            word-break: break-all;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        th,
        td {
            padding: 8px 10px;
            border-bottom: 1px solid #e6e9ef;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #f6f8fb;
        }

        .note {
            padding: 12px 14px;
            border-left: 4px solid #f0ad4e;
            background: #fff8ec;
            border-radius: 4px;
        }

        .empty {
            color: #7a8494;
        }
    </style>
</head>
<body>

<div class="page">
    <h1>API Request Logger</h1>
    <p>每次呼叫 API 時，會將 request body 存成 <code>logs/</code> 內的一個文字檔。</p>

    <div class="card">
        <h2>API 網址</h2>
        <pre><?= htmlspecialchars($apiUrl, ENT_QUOTES, 'UTF-8') ?></pre>
        <p>方法：<code>POST</code>　Content-Type：<code>application/json</code></p>
    </div>

    <div class="card">
        <h2>curl 範例</h2>
        <pre>curl -X POST -H "Content-Type: application/json" -d "{\"name\":\"Alice\",\"score\":100}" <?= htmlspecialchars($apiUrl, ENT_QUOTES, 'UTF-8') ?></pre>
    </div>

    <div class="card">
        <h2>檔案儲存位置與命名</h2>
        <p>儲存位置：本資料夾下的 <code>logs/</code>（不存在時由程式自動建立）。</p>
        <div class="note">
            題目規格的檔名為 <code>HH:MM:SS-request.txt</code>，但 <strong>Windows 檔名不允許含冒號（:）</strong>，
            因此實作改用連字號：<code>HH-MM-SS-request.txt</code>。
            同一秒內重複呼叫時會自動附加序號（例如 <code>14-05-09-request-1.txt</code>）以避免覆蓋。
        </div>
    </div>

    <div class="card">
        <h2>回應狀態碼</h2>
        <table>
            <thead>
                <tr><th>狀態碼</th><th>說明</th></tr>
            </thead>
            <tbody>
                <tr><td>200</td><td>成功，回傳儲存的檔名</td></tr>
                <tr><td>400</td><td>request body 為空或 JSON 格式錯誤</td></tr>
                <tr><td>405</td><td>非 POST 方法</td></tr>
                <tr><td>415</td><td>Content-Type 不是 application/json</td></tr>
                <tr><td>500</td><td>檔案寫入失敗</td></tr>
            </tbody>
        </table>
    </div>

    <div class="card">
        <h2>已記錄的請求（<?= count($logFiles) ?> 筆）</h2>
        <?php if (empty($logFiles)): ?>
            <p class="empty">目前尚無任何紀錄，請先用上方的 curl 範例呼叫 API。</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>檔名</th>
                        <th>時間</th>
                        <th>內容預覽</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logFiles as $file): ?>
                        <?php
                        // 只預覽前 300 個字元，避免頁面過長
                        $content = (string) file_get_contents($file);
                        $preview = mb_strimwidth($content, 0, 300, '...', 'UTF-8');
                        ?>
                        <tr>
                            <td><code><?= htmlspecialchars(basename($file), ENT_QUOTES, 'UTF-8') ?></code></td>
                            <td><?= date('Y-m-d H:i:s', filemtime($file)) ?></td>
                            <td><code><?= htmlspecialchars($preview, ENT_QUOTES, 'UTF-8') ?></code></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
