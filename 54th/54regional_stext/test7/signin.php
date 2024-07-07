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
            if(isset($_SESSION["signin"])){
                header("location: admincomment.php");
            }
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
                圖片驗證碼
                <div class="div">
                    <?php
                        $data=[];
                        for($i=0;$i<4;$i=$i+1){
                            $rand=rand(0,9);
                            ?><img src="verifycode.php?str=<?= $rand ?>" class="verimage" id="<?= $i ?>" data-id="<?= $rand ?>"><?php
                            $data[]=$rand;
                        }
                        sort($data);
                    ?>
                </div>
                由小排到大
                <div class="div dropbox" id="dropbox"></div>
                <div class="div text-center">
                    <input type="button" class="btn btn-light" onclick="location.reload()" value="重設">
                    <input type="button" class="btn btn-light" onclick="location.reload()" value="驗證碼重新產生">
                    <input type="submit" class="btn btn-warning" name="submit" value="送出">
                </div>
                <input type="hidden" id="ver" name="ver">
                <input type="hidden" name="ans" value="<?= implode("",$data) ?>">
            </form>
        </div>

        <?php
            if(isset($_POST["submit"])){
                if($_POST["name"]=="admin"&&$_POST["password"]=="1234"&&$_POST["ver"]==$_POST["ans"]){
                    $_SESSION["signin"]=true;
                    ?><script>alert("登入成功");location.href="verify.php"</script><?php
                }else{
                    ?><script>alert("登入失敗!");location.href="signin.php"</script><?php
                }
            }

        ?>

        <script src="init.js"></script>
        <script src="signin.js"></script>
    </body>
</html>