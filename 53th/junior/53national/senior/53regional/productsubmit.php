<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>咖啡商品展示系統</title>
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <?php
            include("link.php");
            if(!isset($_SESSION["data"])||$_SESSION["permission"]!="管理者"){ header("location:index.php"); }
        ?>
        <h1>網站前台登入頁面</h1>
        <input type="button" class="mbutton" onclick="location.href='main.php'" value="首頁">
        <input type="button" class="mbutton selt" onclick="location.href='productindex.php'" value="上架商品">
        <input type="button" class="mbutton" onclick="location.href='admin.php'" value="會員管理">
        <input type="button" class="mbutton logout" onclick="location.href='link.php?logout='" value="登出">
        <hr>
        <input type="button" class="mbutton" onclick="location.href='productindex.php?clearall='" value="取消"><br><br>
        <input type="button" class="mbutton" onclick="location.href='productindex.php'" value="選擇版型">
        <input type="button" class="mbutton" onclick="location.href='productinput.php'" value="填寫資料">
        <input type="button" class="mbutton" onclick="location.href='productperview.php'" value="預覽">
        <input type="button" class="mbutton selt" onclick="location.href='productsubmit.php'" value="確定送出"><br><br>
        <div class="main">
            <h2 class="mag">確定送出</h2>
            <form>
                請使用者再次確認是否送出?<br><br>
                <input type="submit" name="submit" value="取消">
                <input type="submit" name="submit" value="送出">
            </form>
        </div>
        <?php
            if(isset($_GET["submit"])){
                if($_GET["submit"]=="送出"){
                    $name=$_SESSION["name"];
                    $cost=$_SESSION["cost"];
                    $link=$_SESSION["link"];
                    $intr=$_SESSION["intr"];
                    $picture=$_SESSION["picture"];
                    $val=$_SESSION["val"];
                    query($db,"INSERT INTO `coffee`(`name`,`cost`,`link`,`intr`,`picture`,`date`,`val`)VALUES('$name','$cost','$link','$intr','$picture','$time','$val')");
                }
                unset($_SESSION["name"]);
                unset($_SESSION["cost"]);
                unset($_SESSION["link"]);
                unset($_SESSION["intr"]);
                unset($_SESSION["picture"]);
                unset($_SESSION["val"]);
                ?><script>alert("上傳成功");location.href="main.php"</script><?php
            }
            if(isset($_GET["val"])){
                if($_GET["val"]=="no"){
                    if(!isset($_SESSION["val"])){
                        $_SESSION["val"]="1";
                    }
                }else{
                    $_SESSION["val"]=$_GET["val"];
                }
                ?><script>location.href="productinput.php"</script><?php
            }else{
                if(!isset($_SESSION["val"])){
                    $_SESSION["val"]="1";
                    ?><script>location.href="productinput.php"</script><?php
                }
            }
        ?>
    </body>
</html>