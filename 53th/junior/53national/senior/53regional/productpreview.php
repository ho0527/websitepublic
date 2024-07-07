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
            function data($p){
                if($p=="name"){
                    ?>商品名稱: <?= @$_SESSION["name"] ?><?php
                }elseif($p=="cost"){
                    ?>費用: <?= @$_SESSION["cost"] ?><?php
                }elseif($p=="link"){
                    ?>相關連結: <?= @$_SESSION["link"] ?><?php
                }elseif($p=="date"){
                    ?>發佈日期: (發佈後產生)<?php
                }else{
                    ?>商品簡介: <?= @$_SESSION["intr"] ?><?php
                }
            }
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
                <input type="button" class="mbutton selt" onclick="location.href='productperview.php'" value="預覽">
                <input type="button" class="mbutton" onclick="location.href='productsubmit.php'" value="確定送出"><br><br>
                <div class="main">
                    <h2 class="mag">預覽</h2>
                    <table class="producttable">
                        <?php
                            $val=$_SESSION["val"];
                            $product=fetchall(query($db,"SELECT*FROM `product`"));
                            for($j=0;$j<count($product);$j++){
                                ?>
                                <tr>
                                    <td class="producttd">
                                        <table class="coffeetable">
                                            <?php
                                                if($val==$product[$j][0]&&$product[$j][1]=="picture"){
                                                    ?>
                                                    <tr>
                                                        <td class="coffeetd" rowspan="3"><img src="<?= $_SESSION["picture"] ?>" alt="圖片" width="175px" height="200px"></td>
                                                        <td class="coffeetd"><?php data($product[$j][2]); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="coffeetd"><?php data($product[$j][4]); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="coffeetd"><?php data($product[$j][6]); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="coffeetd"><?php data($product[$j][7]); ?></td>
                                                        <td class="coffeetd"><?php data($product[$j][8]); ?></td>
                                                    </tr>
                                                    <?php
                                                }elseif($val==$product[$j][0]&&$product[$j][2]=="picture"){
                                                    ?>
                                                    <tr>
                                                        <td class="coffeetd"><?php data($product[$j][1]); ?></td>
                                                        <td class="coffeetd" rowspan="3"><img src="<?= $_SESSION["picture"] ?>" alt="圖片" width="175px" height="200px"></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="coffeetd"><?php data($product[$j][3]); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="coffeetd"><?php data($product[$j][5]); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="coffeetd"><?php data($product[$j][7]); ?></td>
                                                        <td class="coffeetd"><?php data($product[$j][8]); ?></td>
                                                    </tr>
                                                    <?php
                                                }elseif($val==$product[$j][0]&&$product[$j][3]=="picture"){
                                                    ?>
                                                    <tr>
                                                        <td class="coffeetd"><?php data($product[$j][1]); ?></td>
                                                        <td class="coffeetd"><?php data($product[$j][2]); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="coffeetd" rowspan="3"><img src="<?= $_SESSION["picture"] ?>" alt="圖片" width="175px" height="200px"></td>
                                                        <td class="coffeetd"><?php data($product[$j][4]); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="coffeetd"><?php data($product[$j][6]); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="coffeetd"><?php data($product[$j][8]); ?></td>
                                                    </tr>
                                                    <?php
                                                }elseif($val==$product[$j][0]&&$product[$j][4]=="picture"){
                                                    ?>
                                                    <tr>
                                                        <td class="coffeetd"><?php data($product[$j][1]); ?></td>
                                                        <td class="coffeetd"><?php data($product[$j][2]); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="coffeetd"><?php data($product[$j][3]); ?></td>
                                                        <td class="coffeetd" rowspan="3"><img src="<?= $_SESSION["picture"] ?>" alt="圖片" width="175px" height="200px"></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="coffeetd"><?php data($product[$j][5]); ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="coffeetd"><?php data($product[$j][7]); ?></td>
                                                    </tr>
                                                    <?php
                                                }
                                            ?>
                                        </table>
                                    </td>
                                </tr>
                                <?php
                            }
                        ?>
                    </table>
                </div>
                <?php
            if(isset($_GET["val"])){
                if($_GET["val"]=="no"){
                    if(!isset($_SESSION["val"])){
                        $_SESSION["val"]="1";
                    }
                }else{
                    $_SESSION["val"]=$_GET["val"];
                }
                ?><script>location.href="productperview.php"</script><?php
            }else{
                if(!isset($_SESSION["val"])){
                    $_SESSION["val"]="1";
                    ?><script>location.href="productperview.php"</script><?php
                }
            }
        ?>
    </body>
</html>