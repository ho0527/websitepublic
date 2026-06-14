<?php
declare(strict_types=1);

$dataFile=__DIR__ . '/data.txt';
$defaultPerPage=5;
$errors=[];

if(!file_exists($dataFile)){
    touch($dataFile);
}

if($_SERVER['REQUEST_METHOD']=='POST'){
    $name=trim($_POST['name'] ?? '');
    $message=trim($_POST['message'] ?? '');
    $perPage=(int)($_POST['per_page'] ?? $defaultPerPage);

    if($name==''){
        $errors[]='請輸入姓名。';
    }

    if($message==''){
        $errors[]='請輸入留言內容。';
    }

    if(!in_array($perPage, $allowedPerPage, true)){
        $perPage=$defaultPerPage;
    }

    if($errors==[]){
        $entry=[
            'name'=> $name,
            'message'=> $message,
            'date'=> date('Y-m-d H:i:s'),
        ];

        $written=file_put_contents(
            $dataFile,
            json_encode($entry, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL,
            FILE_APPEND | LOCK_EX
        );

        if($written==false){
            $errors[]='留言儲存失敗，請稍後再試。';
        } else{
            header('Location: index.php?page=1&per_page=' . $perPage);
            exit;
        }
    }
}

$messages=[];
$lines=file($dataFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

if($lines !==false){
    foreach($lines as $line){
        $decoded=json_decode($line, true);
        if(is_array($decoded)){
            $messages[]=$decoded;
        }
    }
}

usort($messages, static function(array $a, array $b): int{
    return strcmp($b['date'] ?? '', $a['date'] ?? '');
});

$perPage=(int)($_GET['per_page'] ?? $_POST['per_page'] ?? $defaultPerPage);

$totalMessages=count($messages);
$totalPages=max(1,(int) ceil($totalMessages / $perPage));
$page=(int)($_GET['page'] ?? 1);
$page=max(1, min($page, $totalPages));
$offset=($page - 1) * $perPage;
$pageMessages=array_slice($messages, $offset, $perPage);

function h(string $value){
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>C1 分頁留言板</title>
    <style>
        *{ box-sizing: border-box; }
        body{
            margin: 0;
            font-family: Arial, "Microsoft JhengHei", sans-serif;
            background: #f3f4f6;
            color: #111827;
        }
        main{
            max-width: 840px;
            margin: 0 auto;
            padding: 32px 20px 48px;
        }
        h1{
            margin: 0 0 8px;
            font-size: 32px;
        }
        .intro{
            margin: 0 0 24px;
            color: #4b5563;
        }
        .panel{
            background: #ffffff;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .field{
            margin-bottom: 16px;
        }
        label{
            display: block;
            font-weight: 700;
            margin-bottom: 8px;
        }
        input[type="text"],
        input[type="number"],
        textarea,
        select{
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #9ca3af;
            border-radius: 6px;
            font: inherit;
            background: #ffffff;
        }
        textarea{
            min-height: 120px;
            resize: vertical;
        }
        .actions,
        .toolbar,
        .pagination{
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: center;
        }
        .toolbar{
            justify-content: space-between;
            margin: 10px 0px;
        }
        .per-page-form{
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            align-items: end;
        }
        .per-page-form .field{
            margin-bottom: 0;
            min-width: 180px;
        }
        .meta{
            color: #4b5563;
            font-size: 14px;
        }
        .button,
        button{
            display: inline-block;
            padding: 10px 14px;
            border: 1px solid #2563eb;
            border-radius: 6px;
            background: #2563eb;
            color: #ffffff;
            font: inherit;
            text-decoration: none;
            cursor: pointer;
        }
        .button.secondary{
            background: #ffffff;
            color: #1d4ed8;
        }
        .button.current{
            background: #1d4ed8;
            color: #ffffff;
        }
        .error{
            margin-bottom: 16px;
            padding: 12px 14px;
            border: 1px solid #fca5a5;
            border-radius: 6px;
            background: #fef2f2;
            color: #991b1b;
        }
        .message{
            padding: 16px 0;
            border-top: 1px solid #e5e7eb;
        }
        .message:first-of-type{
            border-top: 0;
            padding-top: 0;
        }
        .message:last-of-type{
            padding-bottom: 0;
        }
        .message-header{
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 8px;
        }
        .message-name{
            font-weight: 700;
        }
        .message-date{
            color: #6b7280;
            font-size: 14px;
        }
        .message-body{
            margin: 0;
            white-space: pre-wrap;
            line-height: 1.6;
        }
        .empty{
            color: #6b7280;
            margin: 0;
        }
    </style>
</head>
<body>
    <main>
        <h1>分頁留言板</h1>
        <section class="panel">
            <?php if($errors !==[]): ?>
                <div class="error">
                    <?php foreach($errors as $error): ?>
                        <div><?php echo h($error); ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="post" action="index.php?page=1&per_page=<?php echo $perPage; ?>">
                <div class="field">
                    <label for="name">姓名</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        maxlength="50"
                        value="<?php echo h($_POST['name'] ?? ''); ?>"
                        required
                    >
                </div>

                <div class="field">
                    <label for="message">留言內容</label>
                    <textarea
                        id="message"
                        name="message"
                        maxlength="500"
                        required
                    ><?php echo h($_POST['message'] ?? ''); ?></textarea>
                </div>

                <div class="actions">
                    <input type="hidden" name="per_page" value="<?php echo $perPage; ?>">
                    <button type="submit">送出留言</button>
                </div>
            </form>
        </section>

        <section class="panel">
            <div class="toolbar">
                <div class="meta">目前第 <?php echo $page; ?> / <?php echo $totalPages; ?> 頁，共 <?php echo $totalMessages; ?> 筆留言</div>
                <form class="per-page-form" method="get" action="index.php">
                    <input type="hidden" name="page" value="1">
                    <div class="field">
                        <label for="per_page">每頁顯示筆數</label>
                        <input type="number" id="per_page" name="per_page" value="<?php echo $perPage; ?>">
                    </div>
                    <button type="submit">套用</button>
                </form>
            </div>

            <?php if($pageMessages==[]): ?>
                <p class="empty">目前還沒有留言，歡迎留下第一則訊息。</p>
            <?php else: ?>
                <?php foreach($pageMessages as $item): ?>
                    <article class="message">
                        <div class="message-header">
                            <div class="message-name"><?php echo h((string)($item['name'] ?? '匿名')); ?></div>
                            <div class="message-date"><?php echo h((string)($item['date'] ?? '')); ?></div>
                        </div>
                        <p class="message-body"><?php echo h((string)($item['message'] ?? '')); ?></p>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>

        <nav class="pagination" aria-label="留言分頁">
            <?php if($page > 1): ?>
                <a class="button secondary" href="index.php?page=<?php echo $page - 1; ?>&per_page=<?php echo $perPage; ?>">上一頁</a>
            <?php endif; ?>

            <?php for($i=1; $i <=$totalPages; $i++): ?>
                <a class="button <?php echo $i==$page ? 'current' : 'secondary'; ?>" href="index.php?page=<?php echo $i; ?>&per_page=<?php echo $perPage; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>

            <?php if($page < $totalPages): ?>
                <a class="button secondary" href="index.php?page=<?php echo $page + 1; ?>&per_page=<?php echo $perPage; ?>">下一頁</a>
            <?php endif; ?>
        </nav>
    </main>
</body>
</html>
