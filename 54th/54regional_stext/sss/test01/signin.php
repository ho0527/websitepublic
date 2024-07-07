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
            if(isset($_SESSION["signin"])){ header("location:admincomment.php"); }
        ?>
        <div class="nav" id="nav">
            <div class="title">網站管理 - 登入</div>
        </div>

        <div class="maindiv">
            <form method="POST">
                <div class="margin-5px0px">
                    帳號
                    <input type="text" name="username">
                </div>
                <div class="margin-5px0px">
                    密碼
                    <input type="text" name="password">
                </div>
                <div class="margin-5px0px">
                    圖片驗證碼:<br>
                    <?php
                        $ver=[];
                        for($i=0;$i<4;$i=$i+1){
                            $rand=rand(0,9);
                            ?><img src="verifycode.php?str=<?= $rand; ?>" id="<?= $i; ?>" class="vercode" data-id="<?= $rand; ?>"><?php
                            $ver[]=$rand;
                        }
                        sort($ver);
                    ?><br><br>
                </div>
                請託動圖片驗證碼'由小排到大'
                <div class="dropbox" id="dropbox"></div>
                <div class="text-center">
                    <input type="button" class="btn btn-light" onclick="location.reload()" value="驗證碼重新產生">
                    <input type="button" class="btn btn-light" onclick="location.reload()" value="重設">
                    <input type="submit" class="btn btn-warning" name="submit" value="送出">
                </div>
                <input type="hidden" name="ver" id="ver">
                <input type="hidden" name="ans" value="<?= implode("",$ver); ?>">
            </form>
        </div>

        <?php
            if(isset($_POST["submit"])){
                if($_POST["username"]=="admin"&&$_POST["password"]=="1234"){
                    if($_POST["ver"]==$_POST["ans"]){
                        $_SESSION["signin"]=true;
                        ?><script>alert("登入成功");location.href="admincomment.php"</script><?php
                    }else{
                        ?><script>alert("驗證碼有誤");location.href="signin.php"</script><?php
                    }
                }else{
                    ?><script>alert("帳號或密碼有誤");location.href="signin.php"</script><?php
                }
            }
        ?>

        <script src="init.js"></script>
        <script src="signin.js"></script>
    </body>
</html>