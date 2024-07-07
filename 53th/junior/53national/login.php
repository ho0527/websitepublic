<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>南港展覽館接駁專車</title>
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <div class="navigationbar">
            <div class="navigationbarleft">
                <img src="logo.png" class="logo">
                <div class="maintitle">南港展覽館接駁專車系統</div>
            </div>
            <div class="navigationbarright">
                <input type="button" class="navigationbarbutton" onclick="location.href='index.php'" value="首頁">
                <input type="button" class="navigationbarbutton selectbutton" onclick="location.href='login.php'" value="系統管理">
            </div>
        </div>
        <?php
            include("link.php");
            if(!isset($_SESSION["data"])){
                ?>
                <div class="main">
                    <form method="POST">
                        <div class="indextitle">網站管理-登入</div>
                        <hr>
                        帳號: <input type="text" class="input" name="username" value="<?= @$_SESSION["username"] ?>" autocomplete="off"><br><br>
                        密碼: <input type="password" class="input" name="password" value="<?= @$_SESSION["password"] ?>" autocomplete="off"><br><br>
                        驗證碼: <input type="text" class="input" name="verify" value="<?= @$_SESSION["verify"] ?>" autocomplete="off"><br><br>
                        <?php
                            $a="";
                            for($i=0;$i<4;$i=$i+1){
                                $str=range("0","9");
                                $finalStr=$str[rand(0,9)];
                                $a=$a.$finalStr;
                            }
                        ?>
                        <input type="hidden" name="verifyans" value="<?= $a ?>">
                        <div class="verifybox" id="dragbox">
                            <?php echo($a); ?>
                        </div>
                        <input type="submit" class="button" name="reflashpng" value="重新產生"><br>
                        <input type="button" class="loginbutton button" onclick="location.href='?clear='" value="清除">
                        <input type="submit" class="loginbutton button" name="login" value="登入">
                    </form>
                </div>
                <?php
            }else{ header("location:bus.php"); }
            if(isset($_POST["reflashpng"])){
                @$_SESSION["username"]=$_POST["username"];
                @$_SESSION["password"]=$_POST["password"];
                header("location:login.php");
            }

            if(isset($_GET["clear"])){
                unset($_SESSION["username"]);
                unset($_SESSION["password"]);
                header("location:login.php");
            }

            if(isset($_POST["login"])){
                $username=$_POST["username"];
                $password=$_POST["password"];
                $_SESSION["username"]=$username;
                $_SESSION["password"]=$password;
                $verify=$_POST["verify"];
                $ans=$_POST["verifyans"];
                if($row=query($db,"SELECT*FROM `user` WHERE `username`=?",[$username])[0]){
                    if($row[2]==$password){
                        if($ans==$verify){
                            $_SESSION["data"]=$row[0];
                            ?><script>alert("登入成功");location.href="bus.php"</script><?php
                        }else{
                            ?><script>alert("圖形驗證碼有誤");location.href="login.php"</script><?php
                        }
                    }else{
                        ?><script>alert("密碼有誤");location.href="login.php"</script><?php
                    }
                }else{
                    ?><script>alert("帳號有誤");location.href="login.php"</script><?php
                }
            }
        ?>
        <script src="login.js"></script>
    </body>
</html>