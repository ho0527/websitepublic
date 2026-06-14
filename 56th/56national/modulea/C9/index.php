<?php
declare(strict_types=1);

$dataFile = __DIR__ . '/urls.txt';
$errors = [];
$result = null;

if (!file_exists($dataFile)) {
    touch($dataFile);
}

function h(string $value){
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function loadUrlMap(string $dataFile){
    $map = [];
    $lines = file($dataFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    if ($lines === false) {
        return $map;
    }

    foreach ($lines as $line) {
        [$code, $url] = array_pad(explode("\t", $line, 2), 2, '');
        if ($code !== '' && $url !== '') {
            $map[$code] = $url;
        }
    }

    return $map;
}

function generateCode(array $existingCodes, int $length = 6){
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $maxIndex = strlen($characters) - 1;

    do {
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $characters[random_int(0, $maxIndex)];
        }
    } while (isset($existingCodes[$code]));

    return $code;
}

$urlMap = loadUrlMap($dataFile);

if (isset($_GET['id'])) {
    $requestedCode = trim((string) $_GET['id']);

    if ($requestedCode === '' || !isset($urlMap[$requestedCode])) {
        $errors[] = '查無此短網址代碼。';
    } else {
        header('Location: ' . $urlMap[$requestedCode], true, 302);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $originalUrl = trim((string) ($_POST['url'] ?? ''));

    if ($originalUrl === '' || filter_var($originalUrl, FILTER_VALIDATE_URL) === false) {
        $errors[] = '請輸入有效的完整網址。';
    }

    if ($errors === []) {
        $newCode = generateCode($urlMap);
        $written = file_put_contents(
            $dataFile,
            $newCode . "\t" . $originalUrl . PHP_EOL,
            FILE_APPEND | LOCK_EX
        );

        if ($written === false) {
            $errors[] = '短網址儲存失敗，請稍後再試。';
        } else {
            $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['PHP_SELF'] ?? '/')), '/');
            $shortUrl = $scheme . '://' . $host . ($basePath === '' ? '' : $basePath) . '/?id=' . $newCode;

            $result = [
                'code' => $newCode,
                'original_url' => $originalUrl,
                'short_url' => $shortUrl,
            ];

            $urlMap[$newCode] = $originalUrl;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>C9 檔案縮網址工具</title>
    <style>
        * { box-sizing: border-box; }
        body {
            width: 100vw;
            height: 100vh;
            margin: 0;
            font-family: Arial, "Microsoft JhengHei", sans-serif;
            background: linear-gradient(180deg, #eff6ff 0%, #dbeafe 100%);
            color: #0f172a;
            overflow: hidden;
        }
        main {
            width: min(760px, calc(100vw - 32px));
            margin: 40px auto;
        }
        .card {
            background: #ffffff;
            border: 1px solid #bfdbfe;
            border-radius: 16px;
            padding: 28px;
            box-shadow: 0 18px 45px rgba(30, 64, 175, 0.12);
        }
        h1 {
            margin: 0 0 10px;
            font-size: 32px;
        }
        .intro {
            margin: 0 0 24px;
            color: #334155;
            line-height: 1.7;
        }
        .error,
        .success {
            padding: 14px 16px;
            border-radius: 12px;
            margin-bottom: 18px;
        }
        .error {
            background: #fef2f2;
            border: 1px solid #fca5a5;
            color: #991b1b;
        }
        .success {
            background: #ecfeff;
            border: 1px solid #67e8f9;
            color: #155e75;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 700;
        }
        input[type="url"] {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #93c5fd;
            border-radius: 12px;
            font: inherit;
        }
        input[type="url"]:focus {
            outline: 2px solid #60a5fa;
            border-color: #60a5fa;
        }
        button {
            margin-top: 16px;
            border: 0;
            border-radius: 12px;
            padding: 14px 20px;
            background: #2563eb;
            color: #ffffff;
            font: inherit;
            font-weight: 700;
            cursor: pointer;
        }
        button:hover {
            background: #1d4ed8;
        }
        .result-row {
            margin-top: 10px;
            line-height: 1.8;
            word-break: break-all;
        }
        .result-row a {
            color: #0f766e;
        }
        .hint {
            margin-top: 22px;
            color: #475569;
            font-size: 14px;
        }
        code {
            background: #eff6ff;
            padding: 2px 6px;
            border-radius: 6px;
        }
    </style>
</head>
<body>
    <main>
        <section class="card">
            <h1>C9 檔案縮網址工具</h1>

            <?php if ($errors !== []): ?>
                <div class="error">
                    <?php foreach ($errors as $error): ?>
                        <div><?= h($error) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if ($result !== null): ?>
                <div class="success">
                    <div>短網址建立成功。</div>
                    <div class="result-row">短代碼：<strong><?= h($result['code']) ?></strong></div>
                    <div class="result-row">原始網址：<a href="<?= h($result['original_url']) ?>" target="_blank" rel="noopener noreferrer"><?= h($result['original_url']) ?></a></div>
                    <div class="result-row">短網址：<a href="<?= h($result['short_url']) ?>" target="_blank" rel="noopener noreferrer"><?= h($result['short_url']) ?></a></div>
                </div>
            <?php endif; ?>

            <form method="post" action="">
                <label for="url">完整網址</label>
                <input
                    type="url"
                    id="url"
                    name="url"
                    placeholder="https://example.com/page"
                    value="<?= h((string) ($_POST['url'] ?? '')) ?>"
                    required
                >
                <button type="submit">產生短網址</button>
            </form>

            <p class="hint">測試轉址時可直接打開畫面上產生的短網址，或手動輸入像是 <code>?id=Ab12xY</code> 的參數。</p>
        </section>
    </main>
</body>
</html>
