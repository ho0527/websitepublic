
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
            if(isset($_SESSION["issignin"])){ header("location: admincomment.php"); }
        ?>
        <div class="nav tr" id="nav"></div>

        <div class="main">
            <form method="POST">
                <p>
                    帳號: <input type="text" name="username">
                </p>
                <P>
                    密碼: <input type="text" name="password">
                </P>
                <p>
                    圖形驗證碼:<br>
                    <?php
                    $vercode=[];
                    for($i=0;$i<4;$i=$i+1){
                        $rand=rand(0,9);
                        $vercode[]=$rand;
                        ?><img src="verifycode.php?str=<?= $rand ?>" class="verifycode" id="<?= $i ?>" data-id="<?= $rand ?>"><?php
                    }
                    sort($vercode);
                    ?>
                </p>
                由小到大排列:
                <div class="dropbox" id="dropbox"></div><br>
                <p>
                    <input type="button" onclick="location.reload()" value="驗證碼重新產生">
                    <input type="button" onclick="location.reload()" value="重設">
                    <input type="submit" name="submit" value="送出">
                </p>
                <input type="hidden" name="ver" id="ver">
                <input type="hidden" name="ans" value="<?= implode("",$vercode) ?>">
            </form>
        </div>

        <?php
            if(isset($_POST["submit"])){
                if($_POST["ans"]==$_POST["ver"]){
                    if($_POST["username"]=="admin"&&$_POST["password"]=="1234"){
                        $_SESSION["issignin"]=true;
                        ?><script>alert("登入成功");location.href="admincomment.php"</script><?php
                    }else{
                        ?><script>alert("帳密有誤");location.href="signin.php"</script><?php
                    }
                }else{
                    ?><script>alert("驗證碼輸入錯誤");location.href="signin.php"</script><?php
                }
            }
        ?>

        <script src="init.js"></script>
        <script src="signin.js"></script>
    </body>
</html>