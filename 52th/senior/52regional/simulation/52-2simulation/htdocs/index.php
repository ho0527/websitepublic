<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>TODO工作管理系統</title>
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <?php
            session_start();
        ?>
        <form>
            <h1>TODO</h1>
            <h1>TODO工作管理系統</h1>
            <h3>
                帳號: <input type="text" name="username" value="<?= @$_SESSION["username"] ?>" id="username"><br>
                密碼: <input type="password" name="password" value="<?= @$_SESSION["password"] ?>" id="password"><br>
            </h3>
            <h6>
                <h3>驗證碼</h3>
                <?php
                    for($i=0;$i<3;$i=$i+1){
                        $str=range("a","z");
                        $finalstr=$str[rand(0,25)];
                        ?>
                        <div class="dragbox">
                            <img src="verifycode.php?val=<?= $finalstr ?>" class="drag" id="<?= $finalstr ?>" draggable="true">
                        </div>
                        <?php
                    }
                ?>
            </h6>
            <input type="submit" name="reflash" value="重新產生"><br><br>
            請託動圖片
            <?php
                $bos=["'由大排到小'","'由小排到大'"];
                $key=rand(0,1);
                echo($bos[$key]);
            ?><br>
            <div class="dropbox" id="dropbox"></div><br>
            <input type="submit" name="clear" value="清除">
            <input type="button" onclick="login(<?= $key ?>)" value="登入">
            <?php
                if(isset($_GET["reflash"])){
                    $_SESSION["username"]=$_GET["username"];
                    $_SESSION["password"]=$_GET["password"];
                    header("location:index.php");
                }
                if(isset($_GET["clear"])){
                    session_unset();
                    header("location:index.php");
                }
            ?>
        </form>
        <script src="verifycode.js"></script>
    </body>
</html>