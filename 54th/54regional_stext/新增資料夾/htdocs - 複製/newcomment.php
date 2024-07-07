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
        ?>
        <div class="nav tr" id="nav">
            <div class="title">訪客留言版 - 新增留言</div>
        </div>

        <div class="main">
            <form method="POST">
                <p>姓名<input type="text" name="username" value="<?= @$_SESSION["username"] ?>"></p>
                <div>
                    Email<input type="text" name="email" value="<?= @$_SESSION["email"] ?>" pattern="(.*\..*@.*)|(.*@.*\..*)" required>
                    <div class="textright">
                        <label>
                            顯示<input type="checkbox" name="emailshow" checked>
                        </label>
                    </div>
                </div>
                <div>
                    聯絡電話<input type="text" name="phone" value="<?= @$_SESSION["phone"] ?>" pattern="([0-9]+-+([0-9]*-*)*)|(-+[0-9]+([0-9]*-*)+)" required>
                    <div class="textright">
                        <label>
                            顯示<input type="checkbox" name="phoneshow" checked>
                        </label>
                    </div>
                </div>
                <div>
                    <textarea class="fill" name="content" placeholder="內容"><?= @$_SESSION["content"] ?></textarea>
                </div>
                <p>留言序號<input type="text" name="code" value="<?= @$_SESSION["code"] ?>" pattern="[0-9]{4}" required></p>
                <p class="tc">
                    <input type="button" class="btn btn-light fill" onclick="document.getElementById('file').click()" value="上傳圖片"><br><br>
                    <input type="button" class="btn btn-light" onclick="location.reload()" value="重設">
                    <input type="submit" class="btn btn-warning" name="submit" value="送出">
                </p>
                <input type="file" class="hidden" id="file" accept="image/*">
                <input type="hidden" id="filetext" name="image" value="<?= @$_SESSION["image"] ?>">
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

                query($db,"INSERT INTO `comment`VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)",["",$_POST["image"],$_POST["username"],$_POST["content"],$_POST["email"],$emailshow,$_POST["phone"],$phoneshow,$_POST["code"],"","",$time,"",""]);
                ?><script>alert("上傳成功");location.href="comment.php"</script><?php
            }
        ?>

        <script src="init.js"></script>
        <script src="comment.js"></script>
    </body>
</html>