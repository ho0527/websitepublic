<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>網站前台登入頁面</title>
        <link href="index.css" rel="Stylesheet">
        <link rel="stylesheet" href="../../../plugin/css/chrisplugin.css">
        <script src="../../../plugin/js/chrisplugin.js"></script>
    </head>
    <body>
        <div class="main indexdiv">
            <form>
                <?php
                    include("link.php");
                    // if(isset($_SESSION["data"])){ header("location:verify.php"); }
                ?>
                <h1>電子競技網站管理</h1><hr>
                帳號: <input type="text" class="input" id="username" name="username" value="<?= @$_SESSION["username"] ?>" maxlength="1250"><br><br>
                密碼: <input type="text" class="input" id="password" name="password" value="<?= @$_SESSION["password"] ?>" maxlength="1250"><br><br>
                <?php
                    $_SESSION["verifycode1"]="";
                    $_SESSION["verifycode2"]="";
                    for($i=0;$i<2;$i=$i+1){
                        $str=rand(0,9);
                        ?>
                        <div class="dragbox">
                            <img src="api/verifycode.php?val=<?= $str ?>" draggable="false">
                        </div>
                        <?php
                        $_SESSION["verifycode1"]=$_SESSION["verifycode1"].$str;
                    }
                    $mid=rand(0,1);
                    $plus="+";
                    $mis="-";
                    if($mid==0){
                        ?>
                        <div class="dragbox">
                            <img src="api/verifycode.php?val=<?= $plus ?>" draggable="false">
                        </div>
                        <?php
                    }else{
                        ?>
                        <div class="dragbox">
                            <img src="api/verifycode.php?val=-" draggable="false">
                        </div>
                        <?php
                    }
                    for($i=0;$i<2;$i=$i+1){
                        $str=rand(0,9);
                        ?>
                        <div class="dragbox">
                            <img src="api/verifycode.php?val=<?= $str ?>" draggable="false">
                        </div>
                        <?php
                        $_SESSION["verifycode2"]=$_SESSION["verifycode2"].$str;
                    }
                    if($mid==0){
                        $_SESSION["verifycode"]=((int)$_SESSION["verifycode1"])+((int)$_SESSION["verifycode2"]);
                    }else{
                        $_SESSION["verifycode"]=((int)$_SESSION["verifycode1"])-((int)$_SESSION["verifycode2"]);
                        if($_SESSION["verifycode"]<=0){ header("location:index.php"); }
                    }
                ?><br><br>
                <?php
                    for($i=0;$i<=9;$i=$i+1){
                        ?>
                        <div class="dragbox">
                            <img src="api/verifycode.php?val=<?= $i ?>" id="<?= $i ?>" class="dragimg" draggable="true">
                        </div>
                        <?php
                    }
                ?><br><br>
                圖片驗證碼:<br>
                <div class="dropbox" id="dropbox"></div><br><br>
                <input type="button" class="button" id="reflashpng" value="驗證碼重新產生">
                <input type="button" class="button" id="clear" value="重設">
                <input type="button" class="button" id="login" value="送出">
                <?php
                    if(isset($_GET["reflashpng"])){
                        $_SESSION["username"]=$_GET["username"];
                        $_SESSION["password"]=$_GET["password"];
                        ?><script>location.href="index.php"</script><?php
                    }
                    if(isset($_GET["clear"])){
                        unset($_SESSION["username"]);
                        unset($_SESSION["password"]);
                        ?><script>location.href="index.php"</script><?php
                    }
                ?>
            </form>
        </div>
        <script src="index.js"></script>
        <script src="logincheck.js"></script>
    </body>
</html>