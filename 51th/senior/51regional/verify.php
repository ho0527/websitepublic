<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Document</title>
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <?php
            include("link.php");
            if(!isset($_SESSION["data"])){ header("location:index.php"); }
        ?>
        <div class="main center">
            <div class="sttext big textcenter">請點擊驗證碼讓三字母相同</div>
            <div class="verifydiv" id="verifydiva"></div>
            <div class="verifydiv" id="verifydivb"></div>
            <div class="verifydiv" id="verifydivc"></div><br>
            <div class="textcenter">
                <input type="button" class="stbutton outline large" onclick="location.href='api.php?logout='" value="登出">
                <input type="button" class="stbutton outline large" onclick="location.href='verify.php'" value="更新">
                <input type="button" class="stbutton outline large" onclick="verifysubmit()" value="送出">
            </div>
        <div>
        <script src="verify.js"></script>
    </body>
</html>