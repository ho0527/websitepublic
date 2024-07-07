<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>快樂旅遊網</title>
        <link rel="stylesheet" href="bootstrap.css">
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <?php
            include("link.php");
            if(isset($_SESSION["islogin"])){ header("location: admincomment.php"); }
        ?>
        <div class="nav tr" id="nav"></div>

        <div class="main">
            <form method="POST">
                <p>帳號: <input type="text" name="username"></p>
                <p>密碼: <input type="text" name="password"></p>
                <?php
                    $_SESSION["ans"]=rand("1111","9999");
                ?>
                進階圖形驗證<img src="verifycode.php">
                <p>驗證碼: <input type="text" name="ver"></p>
                <p class="tc">
                    <input type="button" onclick="location.reload()" value="重設">
                    <input type="submit" name="submit" value="送出">
                </p>
                <input type="hidden" name="ans" value="<?= $_SESSION["ans"] ?>">
            </form>
        </div>

        <?php
            if(isset($_POST["submit"])){
                if($_POST["ans"]==$_POST["ver"]){
                    if($_POST["username"]=="admin"&&$_POST["password"]=="1234"){
                        $_SESSION["islogin"]=true;
                        ?><script>alert("登入成功");location.href="admincomment.php"</script><?php
                    }else{
                        ?><script>alert("帳密錯誤");location.href="signup.php"</script><?php
                    }
                }else{
                    ?><script>alert("驗證碼錯誤");location.href="signup.php"</script><?php
                }
            }
        ?>

        <script src="init.js"></script>
    </body>
</html>