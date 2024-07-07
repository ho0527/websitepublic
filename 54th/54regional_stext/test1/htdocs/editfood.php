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
            $row=query($db,"SELECT*FROM `food` WHERE `id`=?",[$_GET["id"]])[0];
        ?>
        <div class="nav tr" id="nav"></div>

        <div class="main">
            <form method="POST">
                <p>名稱: <input type="text" name="name" value="<?= @$row["name"] ?>"></p>
                <p>價格: <input type="text" name="price" value="<?= @$row["price"] ?>"></p>
                <p class="tc">
                    <input type="button" onclick="location.reload()" value="重設">
                    <input type="submit" name="submit" value="送出">
                </p>
                <input type="hidden" id="" name="id" value="<?= @$_GET["id"] ?>">
            </form>
        </div>

        <?php
            if(isset($_POST["submit"])){
                query($db,"UPDATE `food` SET `name`=?,`price`=? WHERE `id`=?",[$_POST["name"],$_POST["price"],$_POST["id"]]);
                ?><script>alert("上傳成功");location.href="adminfood.php"</script><?php
            }
        ?>

        <script src="init.js"></script>
    </body>
</html>