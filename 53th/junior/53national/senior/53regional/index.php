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
            if(isset($_SESSION["data"])){ header("location:verify.php"); }
        ?>
        <div class="main">
            <form>
                <h1>咖啡商品展示系統</h1>
                <hr>
                帳號: <input type="text" class="input" name="username" id="username" value="<?= @$_SESSION["username"] ?>"><br><br>
                密碼: <input type="text" class="input" name="password" id="password" value="<?= @$_SESSION["password"] ?>"><br><br>
                圖形驗證碼<br>
                <?php
                    $_SESSION["verifycode"]="";
                    for($i=0;$i<4;$i=$i+1){
                        $str=array_merge(range("0","9"),range("A","Z"),range("a","z"))[rand(0,61)];
                        ?><?php
                        ?>
                        <div class="dragbox">
                            <img src="verifycode.php?str=<?= $str ?>" class="dragimg" id="<?= $i ?>" data-id="<?= $str ?>" draggable="true">
                        </div>
                        <?php
                        $_SESSION["verifycode"]=$_SESSION["verifycode"].$str;
                    }
                ?><br><br>
                請拖動驗證碼圖片
                <?php
                    $str=["'由大排到小'","'由小排到大'"];
                    $key=rand(0,1);
                    echo($str[$key]);
                    $_SESSION["key"]=$key
                ?><br>
                <div class="dropbox"></div>
                <div class="block"></div><br><br>
                <input type="submit" class="button" name="reflashpng" value="重新產生">
                <input type="submit" class="button" name="clear" value="清除">
                <input type="button" class="button" onclick="login()" value="登入">
            </form>
        </div>
        <?php
            if(isset($_GET["reflashpng"])){
                @$_SESSION["username"]=$_GET["username"];
                @$_SESSION["password"]=$_GET["password"];
                header("location:index.php");
            }
            if(isset($_GET["clear"])){
                session_unset();
                header("location:index.php");
            }
        ?>
        <script src="verifycode.js"></script>
    </body>
</html>