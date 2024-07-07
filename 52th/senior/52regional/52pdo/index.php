<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>網站前台登入介面</title>
        <link href="index.css" rel="Stylesheet">
    </head>
    <body>
        <div class="main">
            <form>
                <?php session_start(); ?>
                <div class="indextitle">TODO工作管理系統</div>
                <hr>
                帳號: <input type="text" class="input" id="username" name="username" value="<?= @$_SESSION["username"] ?>"><br><br>
                密碼: <input type="text" class="input" id="password" name="password" value="<?= @$_SESSION["password"] ?>"><br><br>
                驗證碼:<br>
                <?php
                    for($i=0;$i<3;$i=$i+1){
                        $str=range("a","z");
                        $finalStr=$str[rand(0,25)];
                        ?>
                        <div class="dragbox">
                            <img src="api/verifycode.php?val=<?= $finalStr ?>" id="<?= $finalStr ?>" class="dragimg" draggable="true">
                        </div>
                        <?php
                    }
                ?><br><br>
                請拖動驗證碼
                <?php
                    $key=rand(0,1);
                    $string=array(
                        "'由大排到小'",
                        "'由小排到大'"
                    );
                    echo($string[$key]);
                ?><br>
                <div class="dropbox" id="dropbox"></div><br><br>
                <input type="submit" class="button" name="reflashpng" value="重新產生">
                <input type="submit" class="button" name="clear" value="清除">
                <input type="button" class="button" onclick="loginclick(<?= $key ?>)" value="登入"><br><br>
                <?php
                    if(isset($_GET["reflashpng"])){
                        $_SESSION["username"]=$_GET["username"];
                        $_SESSION["password"]=$_GET["password"];
                        header("location:index.php");
                    }
                    if(isset($_GET["clear"])){
                        unset($_SESSION["username"]);
                        unset($_SESSION["password"]);
                        header("location:index.php");
                    }
                ?>
            </form>
        </div>
        <script src="index.js"></script>
    </body>
</html>