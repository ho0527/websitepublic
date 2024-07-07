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
        ?>
        <div class="nav" id="nav">
            <div class="title">訪客留言版 - 新增留言</div>
        </div>

        <div class="main">
            <form method="POST">
                <div class="div">
                    訪客姓名
                    <input type="text" name="name">
                </div>
                <div class="div">
                    Email
                    <input type="text" name="email" pattern="(.*\..*@.*)|(.*@.*\..*)" required>
                    <div class="text-right">
                        <label>
                            顯示
                            <input type="checkbox" name="emailshow" checked>
                        </label>
                    </div>
                </div>
                <div class="div">
                    聯絡電話
                    <input type="text" name="phone" pattern="([0-9]|-)+" required>
                    <div class="text-right">
                        <label>
                            顯示
                            <input type="checkbox" name="phoneshow" checked>
                        </label>
                    </div>
                </div>
                <div class="div">
                    內容
                    <input type="text" name="content">
                </div>
                <div class="div">
                    序號
                    <input type="text" name="no" pattern="[0-9]{4}" required>
                </div>
                <div class="div">
                    <input type="file" id="file" accept="image/*">
                </div>
                <div class="div text-center">
                    <input type="reset" class="btn btn-light" value="重設">
                    <input type="submit" class="btn btn-warning" name="submit" value="送出">
                </div>
                <input type="hidden" id="image" name="image">
            </form>
        </div>

        <?php
            if(isset($_POST["submit"])){
                query(
                    "INSERT INTO `comment`VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
                    [null,$_POST["image"],$_POST["name"],$_POST["content"],$_POST["email"],isset($_POST["emailshow"])?"checked":"",$_POST["phone"],isset($_POST["phoneshow"])?"checked":"",$_POST["no"],"","",$time,"",""]
                );
                ?><script>alert("新增成功");location.href="comment.php"</script><?php
            }
        ?>

        <script src="init.js"></script>
        <script src="comment.js"></script>
    </body>
</html>