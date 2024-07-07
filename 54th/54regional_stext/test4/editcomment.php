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
            <div class="title">訪客留言版 - 修改留言</div>
        </div>

        <div class="maindiv">
            <form method="POST">
                <div class="margin-5px0px">
                    訪客姓名
                    <input type="text" name="name" value="<?= $row["name"] ?>">
                </div>
                <div class="margin-5px0px">
                    連絡電話
                    <input type="text" name="phone" pattern="([0-9]|-)+" value="<?= $row["phone"] ?>" required>
                    <div class="text-right">
                        <label>
                            顯示
                            <input type="checkbox" name="phoneshow" <?= $row["phoneshow"]=="true"?"checked":"" ?>>
                        </label>
                    </div>
                </div>
                <div class="margin-5px0px">
                    Email
                    <input type="text" name="email" pattern="(.*\..*@.*)|((.*@.*\..*))" value="<?= $row["email"] ?>" required>
                    <div class="text-right">
                        <label>
                            顯示
                            <input type="checkbox" name="emailshow" <?= $row["emailshow"]=="true"?"checked":"" ?>>
                        </label>
                    </div>
                </div>
                <div class="margin-5px0px">
                    留言內容
                    <textarea name="content"><?= $row["content"] ?></textarea>
                </div>
                <div class="text-center">
                    <input type="button" class="btn btn-light" onclick="document.getElementById('file').click()" value="上傳圖片">
                    <input type="reset" class="btn btn-light" value="重設">
                    <input type="submit" class="btn btn-warning" name="submit" value="送出">
                </div>
                <input type="file" class="hidden" id="file">
                <input type="hidden" name="filetext" id="filetext" value="<?= $row["image"] ?>">
                <input type="hidden" name="id" value="<?= $_GET["id"] ?>">
            </form>
        </div>

        <?php
            if(isset($_POST["submit"])){
                query(
                    "UPDATE `comment` SET `image`=?,`name`=?,`content`=?,`email`=?,`emailshow`=?,`phone`=?,`phoneshow`=?,`updatetime`=? WHERE `id`=?",
                    [$_POST["filetext"],$_POST["name"],$_POST["content"],$_POST["email"],isset($_POST["emailshow"])?"true":"",$_POST["phone"],isset($_POST["phoneshow"])?"true":"",$time,$_POST["id"]]
                );
                ?><script>alert("修改成功");location.href="comment.php"</script><?php
            }
        ?>

        <script src="init.js"></script>
        <script src="comment.js"></script>
    </body>
</html>