<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>專案討論系統</title>
        <link rel="stylesheet" href="/index.css">
        <link rel="stylesheet" href="index.css">
        <link rel="stylesheet" href="/chrisplugin/css/chrisplugin.css">
        <script src="https://chrisplugin.pages.dev/js/chrisplugin.js"></script>
    </head>
    <body>
        <?php
            include("link.php");
            if(isset($_SESSION["data"])){ header("location:main.php"); }
        ?>
        <div class="navigationbar">
            <div class="navigationbartitle center">專案討論系統</div>
        </div>
        <div class="main center loginmain">
            <form method="POST" action="login.php">
                <div class="inputmargin">
                    <div class="sttext">帳號</div>
                    <div class="stinput underline endicon">
                        <input type="text" name="username">
                        <div class="icon"><img src="/material/icon/user.svg" class="iconinputicon" draggable="false"></div>
                    </div>
                </div>
                <div class="inputmargin">
                    <div class="sttext">密碼</div>
                    <div class="stinput underline endicon">
                        <input type="text" id="password" name="password">
                        <div class="icon"><img src="/material/icon/eyeclose.svg" class="iconinputicon cursor_pointer" id="passwordicon" draggable="false"></div>
                    </div>
                </div>
                <div class="textcenter">
                    <input type="reset" class="stbutton outline" value="清除">
                    <input type="submit" class="stbutton outline" name="submit" value="登入">
                </div>
            </form>
        </div>
        <script src="index.js"></script>
    </body>
</html>