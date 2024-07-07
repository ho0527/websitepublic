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
                    姓名
                    <input type="text" name="name" value="<?= $row["name"] ?>">
                </div>
                <div class="div">
                    Email
                    <input type="text" name="email" pattern="(.*\..*@.*)|(.*@.*\..*)" value="<?= $row["email"] ?>" required>
                    <div class="text-right">
                        <label>
                            顯示
                            <input type="checkbox" name="emailshow" <?= $row["emailshow"] ?>>
                        </label>
                    </div>
                </div>
                <div class="div">
                    連絡電話
                    <input type="text" name="phone" pattern="([0-9]|-)+" value="<?= $row["phone"] ?>" required>
                    <div class="text-right">
                        <label>
                            顯示
                            <input type="checkbox" name="phoneshow" <?= $row["phoneshow"] ?>>
                        </label>
                    </div>
                </div>
                <div class="div">
                    留言內容
                    <input type="text" name="content" value="<?= $row["content"] ?>">
                </div>
                <input type="file" id="file" accept=".jpg, .png, .gif">
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
                    [$_POST["image"],$_POST["name"],$_POST["content"],$_POST["email"],(isset($_POST["emailshow"])?"checked":""),$_POST["phone"],(isset($_POST["phoneshow"])?"checked":""),$time,$_POST["id"]]
                );
                ?><script>alert("更新成功");location.href="comment.php"</script><?php
            }
        ?>

        <script src="init.js"></script>
        <script src="comment.js"></script>
    </body>
</html>