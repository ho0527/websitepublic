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
            $row=query($db,"SELECT*FROM `comment` WHERE `id`=?",[$_GET["editcomment"]])[0];
        ?>
        <div class="nav tr" id="nav">
            <div class="title">訪客留言版 - 修改留言</div>
        </div>

        <div class="main">
            <form method="POST">
                <p>姓名<input type="text" name="username" value="<?= $row["username"] ?>"></p>
                <p>
                    Email<input type="text" name="email" value="<?= $row["email"] ?>" pattern="(.*\..*@.*)|(.*@.*\..*)" required>
                    <div class="textright">
                        <label>
                            顯示
                            <input type="checkbox" name="emailshow" <?= $row["emailshow"]=="true"?"checked":"" ?>>
                        </label>
                    </div>
                </p>
                <p>
                    聯絡電話<input type="text" name="phone" value="<?= $row["phone"] ?>" pattern="([0-9]+-+([0-9]*-*)*)|(-+[0-9]+([0-9]*-*)+)" required>
                    <div class="textright">
                        <label>
                            顯示
                            <input type="checkbox" name="phoneshow" <?= $row["phoneshow"]=="true"?"checked":"" ?>>
                        </label>
                    </div>
                </p>
                <p>
                    <textarea class="fill" name="content" placeholder="內容"><?= $row["content"] ?></textarea>
                </p>
                <p class="tc">
                    <input type="button" class="btn btn-light fill" onclick="document.getElementById('file').click()" value="上傳圖片"><br><br>
                    <input type="button" class="btn btn-light" onclick="location.reload()" value="重設">
                    <input type="submit" class="btn btn-warning" name="submit" value="送出">
                </p>
                <input type="file" class="hidden" id="file" accept="image/*">
                <input type="hidden" id="filetext" name="image" value="<?= $row["image"] ?>">
                <input type="hidden" id="" name="id" value="<?= @$_GET["editcomment"] ?>">
            </form>
        </div>

        <?php
            if(isset($_POST["submit"])){
                $emailshow="false";
                $phoneshow="false";

                if(isset($_POST["emailshow"])){
                    $emailshow="true";
                }

                if(isset($_POST["phoneshow"])){
                    $phoneshow="true";
                }

                query($db,"UPDATE `comment` SET `image`=?,`username`=?,`content`=?,`email`=?,`emailshow`=?,`phone`=?,`phoneshow`=?,`updatetime`=? WHERE `id`=?",[$_POST["image"],$_POST["username"],$_POST["content"],$_POST["email"],$emailshow,$_POST["phone"],$phoneshow,$time,$_POST["id"]]);
                ?><script>alert("上傳成功");location.href="comment.php"</script><?php
            }
        ?>

        <script src="init.js"></script>
        <script src="comment.js"></script>
    </body>
</html>