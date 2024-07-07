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
            <div class="title">網站管理 - 修改留言</div>
        </div>

        <div class="main">
            <form method="POST">
                <div class="div">
                    訪客姓名
                    <input type="text" name="name" value="<?= $row["name"] ?>">
                </div>
                <div class="div">
                    Email
                    <input type="text" name="email" value="<?= $row["email"] ?>" pattern="(.*\..*@.*)|(.*@.*\..*)" required>
                    <div class="text-right">
                        <label>
                            顯示
                            <input type="checkbox" name="emailshow" <?= $row["emailshow"] ?>>
                        </label>
                    </div>
                </div>
                <div class="div">
                    聯絡電話
                    <input type="text" name="phone" value="<?= $row["phone"] ?>" pattern="([0-9]|-)+" required>
                    <div class="text-right">
                        <label>
                            顯示
                            <input type="checkbox" name="phoneshow" <?= $row["phoneshow"] ?>>
                        </label>
                    </div>
                </div>
                <div class="div">
                    內容
                    <input type="text" name="content" value="<?= $row["content"] ?>">
                </div>
                <div class="div">
                    <input type="file" id="file" accept="image/*">
                </div>
                <div class="div text-center">
                    <input type="reset" class="btn btn-light" value="重設">
                    <input type="submit" class="btn btn-warning" name="submit" value="送出">
                </div>
                <input type="hidden" id="image" name="image" value="<?= $row["image"] ?>">
                <input type="hidden" name="id" value="<?= $_GET["id"] ?>">
            </form>
        </div>

        <?php
            if(isset($_POST["submit"])){
                query(
                    "UPDATE `comment` SET `image`=?,`name`=?,`content`=?,`email`=?,`emailshow`=?,`phone`=?,`phoneshow`=?,`updatetime`=? WHERE `id`=?",
                    [$_POST["image"],$_POST["name"],$_POST["content"],$_POST["email"],isset($_POST["emailshow"])?"checked":"",$_POST["phone"],isset($_POST["phoneshow"])?"checked":"",$time,$_POST["id"]]
                );
                ?><script>alert("修改成功");location.href="admincomment.php"</script><?php
            }
        ?>

        <script src="init.js"></script>
        <script src="comment.js"></script>
    </body>
</html>