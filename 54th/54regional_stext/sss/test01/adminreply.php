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
        <?php include("link.php");$row=query("SELECT*FROM `comment` WHERE `id`=?",[$_GET["id"]])[0] ?>
        <div class="nav" id="adminnav">
            <div class="title">訪客留言版 - 回應留言</div>
        </div>

        <div class="maindiv">
            <form method="POST">
                <div class="margin-5px0px">
                    回應
                    <input type="text" name="reply" value="<?= $row["reply"] ?>">
                </div>
                <div class="text-center">
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
                ?><script>alert("回應成功");location.href="admincomment.php"</script><?php
            }
        ?>

        <script src="init.js"></script>
        <script src="comment.js"></script>
    </body>
</html>