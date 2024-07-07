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
            if(!isset($_SESSION["data"])){ header("location:index.php"); }
            if(isset($_SESSION["pedit"])){
                $number=$_SESSION["pedit"];
                $row=fetch(query($db,"SELECT*FROM `coffee` WHERE `id`='$number'"));
                ?>
                <h1>咖啡商品展示系統</h1>
                <input type="button" class="button" onclick="location.href='main.php'" value="首頁">
                <input type="button" class="button" onclick="location.href='productindex.php'" value="上架商品">
                <input type="button" class="button selt" onclick="location.href='admin.php'" value="會員管理">
                <input type="button" class="button" onclick="location.href='link.php?logout='" value="登出">
                <hr>
                <div class="main">
                    <form id="form" method="POST" enctype="multipart/form-data">
                        <h2>修改商品</h2>
                        商品id <input type="text" name="id" value="<?= @$row[0] ?>" readonly><br><br>
                        商品名稱 <input type="text" name="name" value="<?= @$row["name"] ?>"><br><br>
                        費用 <input type="number" name="cost" value="<?= @$row["cost"] ?>"><br><br>
                        相關連結 <input type="text" name="link" value="<?= @$row["link"] ?>"><br><br>
                        商品簡介 <textarea name="intr" id="" cols="25" rows="3"><?= @$row["intr"] ?></textarea><br><br>
                        圖片<input type="file" name="picture" style="width:175px"><br><br>
                        已上傳:<?php
                            echo($row["picture"]);
                        ?><br><br>
                        版型(可至上架商品看板型id) <input type="text" name="val" value="<?= @$row[7] ?>"><br><br>
                        <input type="button" class="button" onclick="location.href='main.php'" value="返回">
                        <input type="submit" class="button" name="submit" value="送出">
                    </form>
                </div>
                <?php
            }

            if(isset($_GET["pedit"])){
                $_SESSION["pedit"]=$_GET["pedit"];
                ?><script>location.href="edit.php"</script><?php
            }

            if(isset($_POST["submit"])){
                $id=$_POST["id"];
                $name=$_POST["name"];
                $link=$_POST["link"];
                $cost=$_POST["cost"];
                $intr=$_POST["intr"];
                $val=$_POST["val"];
                if(block($name)||block($link)||block($cost)||block($intr)){
                    ?><script>alert("禁止輸入特殊符號");location.href="edit.php"</script><?php
                }elseif(!$row=fetch(query($db,"SELECT*FROM `product` WHERE `id`='$val'"))){
                    ?><script>alert("找不到此版型");location.href="edit.php"</script><?php
                }else{
                    if(!empty($_FILES["picture"]["name"])){
                        if(block($_FILES["picture"]["name"])){
                            ?><script>alert("檔名禁止輸入特殊符號");location.href="edit.php"</script><?php
                        }else{
                            move_uploaded_file($_FILES["picture"]["tmp_name"],"image/".$_FILES["picture"]["name"]);
                            $picture="image/".$_FILES["picture"]["name"];
                            query($db,"UPDATE `coffee` SET `name`='$name',`link`='link',`cost`='$cost',`intr`='$intr',`val`='$val',`picture`='$picture' WHERE `id`='$id'");
                        }
                    }else{
                        query($db,"UPDATE `coffee` SET `name`='$name',`link`='link',`cost`='$cost',`intr`='$intr',`val`='$val' WHERE `id`='$id'");
                    }
                    ?><script>alert("修改成功");location.href="main.php"</script><?php
                }
            }
        ?>
    </body>
</html>