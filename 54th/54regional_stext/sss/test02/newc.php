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
            <div class="title">留言管理 - 新增留言</div>
        </div>

        <div class="maindiv">
            <form method="POST">
                <div class="margin">
                    姓名
                    <input type="text" name="name">
                </div>
                <div class="margin">
                    電話
                    <input type="text" name="phone" pattern="([0-9]|-)+" required>
                    <div class="text-right">
                        顯示<input type="checkbox" name="phonecheck" checked>
                    </div>
                </div>
                <div class="margin">
                    email
                    <input type="text" name="email" pattern="(.*\..*@.*)|(.*@.*\..*)" required>
                    <div class="text-right">
                        顯示<input type="checkbox" name="emailcheck" checked>
                    </div>
                </div>
                <div class="margin">
                    內容
                    <textarea name="content"></textarea>
                </div>
                <div class="margin">
                    序號
                    <input type="text" name="no" pattern="[0-9]{4}">
                </div>
                <div class="text-center">
                    <input type="button" class="btn btn-light" onclick="document.getElementById('file').click()" value="上傳圖片">
                    <input type="reset" class="btn btn-light" value="重設">
                    <input type="submit" class="btn btn-warning" name="submit" value="送出">
                </div>
                <input type="file" class="hidden" id="file">
                <input type="hidden" id="image" name="image">
            </form>
        </div>

        <?php
            if(isset($_POST["submit"])){
                query("INSERT INTO `comment`(`image`,`name`,`content`,`email`,`emailshow`,`phone`,`phoneshow`,`no`,`pin`,`reply`,`createtime`,`updatetime`,`deletetime`)VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)",[$_POST["image"],$_POST["name"],$_POST["content"],$_POST["email"],isset($_POST["emailshow"])?"true":"",$_POST["phone"],isset($_POST["phoneshow"])?"true":"",$_POST["no"],"","",$time,"",""])
                ?><script>alert("新增成功");location.href="comment.php"</script><?php
            }
        ?>

        <script src="init.js"></script>
        <script src="comment.js"></script>
    </body>
</html>