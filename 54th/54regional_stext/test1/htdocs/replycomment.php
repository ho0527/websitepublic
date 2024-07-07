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
            $row=query($db,"SELECT*FROM `comment` WHERE `id`=?",[$_GET["id"]])[0];
        ?>
        <div class="nav tr" id="nav"></div>

        <div class="main">
            <form method="POST">
                <p>回應: <input type="text" name="reply" value="<?= @$row["reply"] ?>"></p>
                <p class="tc">
                    <input type="button" onclick="location.reload()" value="重設">
                    <input type="submit" name="submit" value="送出">
                </p>
                <input type="hidden" id="" name="id" value="<?= @$_GET["id"] ?>">
            </form>
        </div>

        <?php
            if(isset($_POST["submit"])){
                query($db,"UPDATE `comment` SET `reply`=? WHERE `id`=?",[$_POST["reply"],$_POST["id"]]);
                ?><script>alert("上傳成功");location.href="admincomment.php"</script><?php
            }
        ?>

        <script src="init.js"></script>
    </body>
</html>