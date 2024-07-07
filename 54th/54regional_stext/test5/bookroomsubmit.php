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
            <div class="title">訪客訂房 - 填寫聯絡方式</div>
        </div>

        <div class="maindiv">
            <form method="POST">
                <div class="div">
                    姓名
                    <input type="text" name="name" required>
                </div>
                <div class="div">
                    E-mail
                    <input type="text" name="email" required>
                </div>
                <div class="div">
                    電話
                    <input type="text" name="phone" required>
                </div>
                <div class="div">
                    備註
                    <input type="text" name="ps" required>
                </div>
                <div class="div text-center">
                    <input type="reset" class="btn btn-light" value="重設">
                    <input type="submit" class="btn btn-warning" name="submit" value="送出">
                </div>
                <input type="hidden" id="totaldate" name="totaldate">
                <input type="hidden" id="startday" name="startday">
                <input type="hidden" id="endday" name="endday">
                <input type="hidden" id="room" name="room">
                <input type="hidden" id="price" name="price">
            </form>
        </div>

        <?php
            if(isset($_POST["submit"])){
                query(
                    "INSERT INTO `bookroom`VALUES(?,?,?,?,?,?,?,?,?)",
                    [null,$_POST["startday"],$_POST["endday"],$_POST["room"],$_POST["name"],$_POST["phone"],$_POST["email"],$_POST["ps"],$_POST["price"]]
                );
                alert("新增成功","no.php?no=20240309".padstart(4,query("SELECT*FROM `bookroom` ORDER BY `id` DESC")[0]["id"]));
            }
        ?>

        <script src="init.js"></script>
        <script src="bookroomsubmit.js"></script>
    </body>
</html>