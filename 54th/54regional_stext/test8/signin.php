<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <link rel="stylesheet" href="bootstrap.css">
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <?php
            include("link.php");
            if(isset($_SESSION["signin"])){ header("location: admincomment.php"); }
        ?>
        <div class="nav" id="nav">
            <div class="title">網站管理 - 登入</div>
        </div>

        <div class="main">
            <form method="POST">
                <div class="div">
                    帳號
                    <input type="text" name="name">
                </div>
                <div class="div">
                    密碼
                    <input type="text" name="password">
                </div>
                <div class="div" id="div">
                    圖片驗證碼<br>
                </div>
                <div class="div">
                    請輸入驗證碼由小排到大
                    <input type="text" name="ans" id="ans">
                </div>
                <div class="div text-center">
                    <input type="button" class="btn btn-light" onclick="ref()" value="驗證碼重新產生">
                    <input type="button" class="btn btn-light" onclick="location.reload()" value="重設">
                    <input type="submit" class="btn btn-warning" name="submit" value="送出">
                </div>
                <input type="hidden" name="verifycode" id="verifycode">
            </form>
        </div>

        <?php
            if(isset($_POST["submit"])){
                if($_POST["name"]=="admin"&&$_POST["password"]=="1234"&&$_POST["verifycode"]==$_POST["ans"]){
                    $_SESSION["signin"]=true;
                    ?><script>alert("登入成功");location.href="verify.php"</script><?php
                }else{
                    ?><script>alert("登入失敗");location.href="signin.php"</script><?php
                }
            }
        ?>

        <script src="init.js"></script>
        <script src="signin.js"></script>
    </body>
</html>