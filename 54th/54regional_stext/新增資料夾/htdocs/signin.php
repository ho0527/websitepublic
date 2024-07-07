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
        <div class="nav tr" id="nav">
            <div class="title">網站管理</div>
        </div>

        <div class="main">
            <form method="POST" id="form">
                <p>帳號<input type="text" name="username"></p>
                <p>密碼<input type="text" name="password"></p>
                <div class="vercodediv">
                    圖形驗證(由小排到大):<br>
                    <?php
                        $_SESSION["ans"]=[];
                        for($i=0;$i<4;$i=$i+1){
                            $str=(string)rand(0,9);
                            $_SESSION["ans"][]=$str;
                            ?>
                            <img src="verifycode.php?str=<?= $str ?>" class="vercode" id="<?= $i ?>" data-id="<?= $str ?>">
                            <?php
                        }
                        sort($_SESSION["ans"]);
                    ?>
                </div>
                <div class="dropbox" id="dropbox"></div>
                <p class="tc">
                    <input type="button" class="btn btn-light" onclick="location.reload()" value="驗證碼重新產生">
                    <input type="button" class="btn btn-light" onclick="location.reload()" value="重設">
                    <input type="submit" class="btn btn-warning" name="submit" value="送出">
                </p>
                <input type="hidden" name="ans" value="<?= implode("",$_SESSION["ans"]) ?>">
                <input type="hidden" name="ver" id="ver" value="">
            </form>
        </div>

        <?php
            if(isset($_POST["submit"])){
                if($_POST["ans"]==$_POST["ver"]){
                    if($_POST["username"]=="admin"&&$_POST["password"]=="1234"){
                        $_SESSION["islogin"]=true;
                        ?><script>alert("登入成功");location.href="admincomment.php"</script><?php
                    }else{
                        ?><script>alert("帳密錯誤");location.href="signin.php"</script><?php
                    }
                }else{
                    ?><script>alert("驗證碼錯誤");location.href="signin.php"</script><?php
                }
            }
        ?>

        <script src="init.js"></script>
        <script src="signin.js"></script>
    </body>
</html>