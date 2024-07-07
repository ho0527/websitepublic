<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>網路問卷調查系統</title>
        <link rel="stylesheet" href="index.css">
        <link rel="stylesheet" href="/chrisplugin/css/chrisplugin.css">
        <script src="https://chrisplugin.pages.dev/js/chrisplugin.js"></script>
    </head>
    <body>
        <div id="main"></div>
        <?php
            include("link.php");
            if(!isset($_SESSION["data"])){
                ?>
                <div class="navigationbar">
                    <div class="navigationbartitle center">網路問卷調查系統</div>
                </div>
                <div class="main indexmain center">
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
                            <input type="button" class="stbutton outline" onclick="location.href='signup.php'" value="註冊">
                            <input type="reset" class="stbutton outline" value="清除">
                            <input type="submit" class="stbutton outline" name="login" value="登入">
                        </div>
                    </form><br>
                    <form>
                        <div class="sttext big">填寫問卷網址:</div><br>
                        <div class="stinput underline indexinput">
                            <input type="text" name="text" placeholder="請輸入網址">
                        </div>
                        <input type="submit" class="stbutton outline" name="submit" value="送出">
                    </form>
                </div>
                <?php
            }else{
                ?>
                <div class="navigationbar">
                    <div class="navigationbarleft"><div class="navigationbartitle">網路問卷管理系統</div></div>
                    <div class="navigationbarright">
                        <input type="button" class="navigationbarbutton" onclick="location.href='verify.php'" value="返回">
                        <input type="submit" class="navigationbarbutton" onclick="location.href='api.php?logout='" value="登出">
                    </div>
                </div>
                <div class="main indexmain center">
                    <form>
                        <div class="sttext big">填寫問卷網址:</div><br>
                        <div class="stinput underline indexinput">
                            <input type="text" name="text" placeholder="請輸入網址">
                        </div>
                        <input type="submit" class="stbutton outline" name="submit" value="送出">
                    </form>
                </div>
                <?php
            }
        ?>
        <?php
            if(isset($_POST["login"])){
                $username=$_POST["username"];
                $password=$_POST["password"];
                if($row=query($db,"SELECT*FROM `user` WHERE `username`=?",[$username])[0]){
                    if($row[2]==$password){
                        query($db,"INSERT INTO `log`(`username`,`move`,`movetime`)VALUES('$username','登入系統','$time')");
                        session_unset();
                        $_SESSION["data"]=$row[0];
                        ?><script>alert("登入成功");location.href="verify.php"</script><?php
                    }else{
                        query($db,"INSERT INTO `log`(`username`,`move`,`movetime`)VALUES('$username','登入失敗(password)','$time')");
                        ?><script>alert("密碼有誤");location.href="index.php"</script><?php
                    }
                }else{
                    query($db,"INSERT INTO `log`(`username`,`move`,`movetime`)VALUES('$username','登入失敗(account)','$time')");
                    ?><script>alert("帳號有誤");location.href="index.php"</script><?php
                }
            }

            if(isset($_GET["submit"])){
                $code=$_GET["text"];
                $data=$_SESSION["data"];
                if(!isset($data)){ $data=""; }
                if($row=query($db,"SELECT*FROM `questioncode` WHERE `code`='$code'")[0]){
                    if($data==$row[2]||$row[2]==""){
                        $_SESSION["id"]=$row[1];
                        query($db,"INSERT INTO `log`(`username`,`move`,`movetime`)VALUES('$data','填寫問卷','$time')");
                        ?><script>location.href="user.php"</script><?php
                    }else{
                        ?><script>alert("使用者錯誤，請先登入後使用");location.href="index.php"</script><?php
                    }
                }else{
                    ?><script>alert("查無此邀請碼");location.href="index.php"</script><?php
                }
            }
        ?>
        <script src="index.js"></script>
    </body>
</html>