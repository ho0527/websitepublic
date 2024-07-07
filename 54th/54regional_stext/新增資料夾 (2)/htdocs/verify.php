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
            if(!isset($_SESSION["signin"])){ header("location: signin.php"); }
        ?>
        <div class="nav" id="nav">
            <div class="title">進階驗證</div>
        </div>

        <div class="main">
            <input type="button" onclick="alert('驗證成功');location.href='admincomment.php'" value="送出">
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