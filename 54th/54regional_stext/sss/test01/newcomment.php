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
        <?php include("link.php"); ?>
        <div class="nav" id="nav">
            <div class="title">訪客留言版 - 新增留言</div>
        </div>

        <div class="maindiv">
            <form method="POST">
                <div class="margin-5px0px">
                    訪客姓名<input type="text" name="name">
                </div>
                <div class="margin-5px0px">
                    連絡電話
                    <input type="text" name="phone" pattern="([0-9]|-)+" required>
                    <div class="text-right">
                        <label>顯示
                            <input type="checkbox" name="phoneshow" checked>
                        </label>
                    </div>
                </div>
                <div class="margin-5px0px">
                    Email
                    <input type="text" name="email" pattern="(.*\..*@.*)|(.*@.*\..*)" required>
                    <div class="text-right">
                        <label>顯示
                            <input type="checkbox" name="emailshow" checked>
                        </label>
                    </div>
                </div>
                <div class="margin-5px0px">
                    內容<textarea name="content"></textarea>
                </div>
                <div class="margin-5px0px">
                    編號<input type="text" name="no" pattern="[0-9]{4}" required>
                </div>
                <div class="text-center">
                    <input type="button" class="btn btn-light" onclick="document.getElementById('file').click()" value="上傳圖片">
                    <input type="button" class="btn btn-light" onclick="location.reload()" value="重設">
                    <input type="submit" class="btn btn-warning" name="submit" value="送出">
                </div>
                <input type="file" class="hidden" id="file">
                <input type="hidden" name="image" id="image">
            </form>
        </div>

        <?php
            if(isset($_POST["submit"])){
                query(
                    "INSERT INTO `comment`(`image`,`name`,`content`,`email`,`emailshow`,`phone`,`phoneshow`,`no`,`pin`,`reply`,`createtime`,`updatetime`,`deletetime`)VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)",
                    [$_POST["image"],$_POST["name"],$_POST["content"],$_POST["email"],isset($_POST["emailshow"])?"true":"",$_POST["phone"],isset($_POST["phoneshow"])?"true":"",$_POST["no"],"","",$time,"",""]
                );
                ?><script>alert("新增成功");location.href="comment.php"</script><?php
            }
        ?>

        <script src="init.js"></script>
        <script src="comment.js"></script>
    </body>
</html>