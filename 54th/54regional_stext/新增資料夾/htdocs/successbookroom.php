<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>快樂旅遊網</title>
        <link rel="stylesheet" href="bootstrap.css">
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <?php
            include("link.php");
        ?>
        <div class="nav" id="nav">
            <div></div>
        </div>
        <div class="nav2">
            <div class="title2">訂房完成</div>
        </div>

        <div class="main textcenter" id="check">
            訂房成功!<br>
            訂房編號: <?= $_GET["no"] ?><br><br>
            <input type="button" class="btn btn-warning fill" onclick="location.href='index.php'" value="返回首頁">
        </div>

        <script src="init.js"></script>
    </body>
</html>