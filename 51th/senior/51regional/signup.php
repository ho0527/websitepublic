<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>signup</title>
        <link rel="stylesheet" href="index.css">
        <link rel="stylesheet" href="/chrisplugin/css/chrisplugin.css">
        <script src="https://chrisplugin.pages.dev/js/chrisplugin.js"></script>
    </head>
    <body>
        <div class="navigationbar">
            <div class="navigationbartitle center">網路問卷調查系統</div>
        </div>
        <div class="main center">
            <form method="POST">
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
                    <input type="button" class="stbutton outline" onclick="location.href='index.php'" value="返回">
                    <input type="reset" class="stbutton outline" value="清除">
                    <input type="submit" class="stbutton outline" name="login" value="登入">
                </div>
            </form>
        </div>
    <?php
        include("link.php");
        if(isset($_POST["username"])){
            $username=$_POST["username"];
            $password=$_POST["password"];
            if(query($db,"SELECT*FROM `user` WHERE `username`=?",[$username])){
                echo("帳號已被註冊");
            }elseif($username==""||$password==""){
                echo("請輸入帳密");
            }else{
                query($db,"INSERT INTO `user`(`username`,`password`)VALUES(?,?)",[$username,$password]);
                ?><script>alert("新增成功");location.href="index.php"</script><?php
            }
        }
    ?>
    </body>
</html>