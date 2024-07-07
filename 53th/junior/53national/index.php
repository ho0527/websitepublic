<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Document</title>
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <div class="navigationbar">
            <div class="navigationbarleft">
                <img src="logo.png" class="logo">
                <div class="maintitle">南港展覽館接駁專車系統</div>
            </div>
            <div class="navigationbarright">
                <input type="button" class="navigationbarbutton selectbutton" onclick="location.href='index.php'" value="首頁">
                <input type="button" class="navigationbarbutton" onclick="location.href='login.php'" value="系統管理">
            </div>
        </div>
        <div class="indexmain">
            <div class="indexmainbody macossectiondiv" id="maindiv">
            </div>
        </div>
        <script src="index.js"></script>
    </body>
</html>