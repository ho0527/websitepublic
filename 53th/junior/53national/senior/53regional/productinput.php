<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>登入失敗</title>
        <link rel="stylesheet" href="index.css">
        <style>
            .main{
                width: 25%;
            }
        </style>
    </head>
    <body>
        <?php
            include("link.php");
            if(isset($_SESSION["data"])){ header("main.php"); }
            unset($_SESSION["edit"]);
            unset($_SESSION["del"]);
        ?>
        <h1>咖啡商品展示系統</h1>
        <input type="button" class="button" onclick="location.href='main.php'" value="首頁">
        <input type="button" class="button selt" onclick="location.href='productindex.php'" value="上架商品">
        <input type="button" class="button" onclick="location.href='admin.php'" value="會員管理">
        <input type="button" class="button" onclick="location.href='link.php?logout='" value="登出">
        <hr>
        <input type="button" class="button" onclick="location.href='productindex.php?clearall='" value="清除">
        <input type="button" class="button" onclick="location.href='productindex.php'" value="選擇版型">
        <input type="button" class="button selt" onclick="location.href='productinput.php'" value="填寫資料">
        <input type="button" class="button" onclick="sub()" value="預覽">
        <input type="button" class="button" onclick="sub()" value="確定送出"><br><br>
        <div class="main">
            <h2>填寫資料</h2><br>
            <form id="form" method="POST" enctype="multipart/form-data">
                商品名稱 <input type="text" name="name" value="<?= @$_SESSION["name"] ?>"><br><br>
                費用 <input type="number" name="cost" value="<?= @$_SESSION["cost"] ?>"><br><br>
                相關連結 <input type="text" name="link" value="<?= @$_SESSION["link"] ?>"><br><br>
                商品簡介 <textarea name="intr" id="" cols="25" rows="3"><?= @$_SESSION["intr"] ?></textarea><br><br>
                圖片<input type="file" name="picture" style="width:175px"><br><br>
                已上傳:<?php
                    if(isset($_SESSION["picture"])){
                        echo($_SESSION["picture"]);
                    }else{
                        echo("無");
                    }
                ?><br><br>
                <input type="submit" name="clear" value="清除">
                <input type="submit" name="submit" value="送出">
            </form>
        </div>
        <?php
            if(isset($_POST["submit"])){
                $_SESSION["name"]=$_POST["name"];
                $_SESSION["link"]=$_POST["link"];
                $_SESSION["cost"]=$_POST["cost"];
                $_SESSION["intr"]=$_POST["intr"];
                $name=$_SESSION["name"];
                $link=$_SESSION["link"];
                $cost=$_SESSION["cost"];
                $intr=$_SESSION["intr"];
                if(block($name)||block($link)||block($cost)||block($intr)){
                    ?><script>alert("禁止輸入特殊符號");location.href="productinput.php"</script><?php
                }else{
                    if(!empty($_FILES["picture"]["name"])){
                        if(block($_FILES["picture"]["name"])){
                            ?><script>alert("檔名禁止輸入特殊符號");location.href="productinput.php"</script><?php
                        }else{
                            move_uploaded_file($_FILES["picture"]["tmp_name"],"image/".$_FILES["picture"]["name"]);
                            $_SESSION["picture"]="image/".$_FILES["picture"]["name"];
                        }
                    }
                    ?><script>location.href="productperview.php"</script><?php
                }
            }
            if(isset($_POST["clear"])){
                unset($_SESSION["name"]);
                unset($_SESSION["picture"]);
                unset($_SESSION["cost"]);
                unset($_SESSION["link"]);
                unset($_SESSION["intr"]);
                ?><script>location.href="productinput.php"</script><?php
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
        <script src="porduct.js"></script>
    </body>
</html>