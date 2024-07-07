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
            $row=query("SELECT*FROM `comment` WHERE `id`=?",[$_GET["id"]])[0];
        ?>
        <div class="nav" id="nav">
            <div class="title">網站管理 - 回應留言</div>
        </div>

        <div class="main">
            <form method="POST">
                <div class="div">
                    回應
                    <input type="text" name="reply" value="<?= $row["reply"] ?>">
                </div>
                <div class="div text-center">
                    <input type="reset" class="btn btn-light" value="重設">
                    <input type="submit" class="btn btn-warning" name="submit" value="送出">
                </div>
                <input type="hidden" name="id" value="<?= $_GET["id"] ?>">
            </form>
        </div>

        <?php
            if(isset($_POST["submit"])){
                query(
                    "UPDATE `comment` SET `reply`=? WHERE `id`=?",
                    [$_POST["reply"],$_POST["id"]]
                );
                ?><script>alert("更新成功");location.href="admincomment.php"</script><?php
            }
        ?>

        <script src="init.js"></script>
        <script src="comment.js"></script>
    </body>
</html>