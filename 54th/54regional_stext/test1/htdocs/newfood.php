<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>不快樂旅遊網:(</title>
        <link rel="stylesheet" href="bootstrap.css">
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <?php
            include("link.php");
            if(!isset($_SESSION["islogin"])){ header("location: signup.php"); }
        ?>
        <div class="nav tr" id="nav"></div>
        <div class="nav2 tc">
            <input type="button" class="btn-outline-primary" onclick="location.href='admincomment.php'" value="留言管理">
            <input type="button" class="btn-outline-primary" onclick="location.href='adminbookroom.php'" value="訂房管理">
            <input type="button" class="btn-outline-primary" onclick="location.href='adminorderfood.php'" value="訂餐管理">
            <input type="button" class="btn-outline-primary active" onclick="location.href='adminfood.php'" value="餐點管理">
        </div>

        <div class="main">
            <form method="POST">
                <p>名稱: <input type="text" name="name" value="<?= @$_SESSION["username"] ?>" required></p>
                <p>價格: <input type="number" name="price" value="<?= @$_SESSION["code"] ?>" min="1" step="1" required></p>
                <p class="tc">
                    <input type="button" onclick="location.reload()" value="重設">
                    <input type="submit" name="submit" value="送出">
                </p>
            </form>
        </div>

        <?php
            if(isset($_POST["submit"])){
                query($db,"INSERT INTO `food`VALUES(?,?,?)",["",$_POST["name"],$_POST["price"]]);
                ?><script>alert("上傳成功");location.href="adminfood.php"</script><?php
            }
        ?>

        <script src="init.js"></script>
    </body>
</html>