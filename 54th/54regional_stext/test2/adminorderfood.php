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
            if(!isset($_SESSION["issignin"])){ header("location: signin.php"); }
        ?>
        <div class="nav tr" id="">
            <div></div>
            <div class="navdiv">
                <input type="button" class="btn-primary" id="index.php" onclick="location.href='index.php'" value="首頁">
                <input type="button" class="btn-primary" id="comment.php" onclick="location.href='comment.php'" value="訪客留言">
                <input type="button" class="btn-primary" id="bookroom.php" onclick="location.href='bookroom.php'" value="訪客訂房">
                <input type="button" class="btn-primary" id="orderfood.php" onclick="location.href='orderfood.php'" value="訪客訂餐">
                <input type="button" class="btn-primary" id="info.php" onclick="location.href='info.php'" value="交通資訊">
                <input type="button" class="btn-primary" id="signin.php" onclick="location.href='api.php?signout='" value="登出">
            </div>
        </div>
        <div class="nav2 tr" id="">
            <input type="button" class="btn-outline-primary" id="admincomment.php" onclick="location.href='admincomment.php'" value="首頁">
            <input type="button" class="btn-outline-primary" id="adminbookroom.php" onclick="location.href='adminbookroom.php'" value="訂房管理">
            <input type="button" class="btn-outline-primary active" id="admin.php" onclick="location.href='adminorderfood.php'" value="訂餐管理">
        </div>

        <script src="init.js"></script>
        <script src="signin.js"></script>
    </body>
</html>