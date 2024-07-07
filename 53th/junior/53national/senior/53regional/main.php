<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>網站前台登入介面</title>
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <?php
            include("link.php");
            if(!isset($_SESSION["data"])){ header("location:index.php"); }
        ?>
        <h1>咖啡商品展示系統</h1>
        <div class="navigationbar">
            <input type="button" class="button selectbutton" onclick="location.href='main.php'" value="首頁">
            <input type="button" class="button" onclick="location.href='productindex.php'" value="上架商品">
            <input type="button" class="button" onclick="location.href='admin.php'" value="會員管理">
            <input type="button" class="button logout" onclick="location.href='link.php?logout='" value="登出">
        </div>
        <hr>
        <div class="main">
        </div>
    </body>
</html>