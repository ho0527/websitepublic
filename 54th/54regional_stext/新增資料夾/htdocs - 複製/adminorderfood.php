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
            if(!isset($_SESSION["islogin"])){ header("location: signup.php"); }
        ?>
        <div class="nav tr" id="adminnav">
            <div class="title">網站管理 - 訂餐管理</div>
        </div>
        <div class="nav2 tc">
            <div class="btn-group">
                <input type="button" class="btn btn-outline-light" onclick="location.href='admincomment.php'" value="留言管理">
                <input type="button" class="btn btn-outline-light" onclick="location.href='adminbookroom.php'" value="訂房管理">
                <input type="button" class="btn btn-outline-light active" onclick="location.href='adminorderfood.php'" value="訂餐管理">
                <input type="button" class="btn btn-outline-light" onclick="location.href='adminfood.php'" value="餐點管理">
            </div>
        </div>

        <script src="init.js"></script>
    </body>
</html>