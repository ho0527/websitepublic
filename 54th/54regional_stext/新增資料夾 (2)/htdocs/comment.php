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
        <div class="nav" id="adminnav">
            <div class="title">訪客留言版</div>
        </div>
        <div class="nav2" id="adminnav">
            <div>
                <span class="title2">訪客留言列表</span>
                <a href="newcomment.php" class="btn btn-warning">新增留言</a>
            </div>
            <div class="btn-group">
                <a href="comment.php" class="btn btn-outline-light">留言管理</a>
                <a href="bookroom.php" class="btn btn-outline-light">訂房管理</a>
                <a href="orderfood.php" class="btn btn-outline-light">訂餐管理</a>
            </div>
        </div>

        <div class="commentmain">
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
        <script src="comment.js"></script>
    </body>
</html>