<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <link rel="stylesheet" href="bootstrap.css">
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <?php
            include("link.php");
            if(!isset($_SESSION["signin"])){ header("location: signin.php"); }
        ?>
        <div class="nav" id="adminnav">
            <div class="title">網站管理 - 訂餐管理</div>
        </div>
        <div class="nav2">
            <div class="btn-group">
                <a href="admincomment.php" class="btn btn-outline-light">留言管理</a>
                <a href="adminbookroom.php" class="btn btn-outline-light">訂房管理</a>
                <a href="adminfoodorder.php" class="btn btn-outline-light active">訂餐管理</a>
            </div>
        </div>

        <script src="init.js"></script>
        <script src="signin.js"></script>
    </body>
</html>