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

        <div class="main">
            <form method="POST" id="main">
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
                    <input type="submit" class="btn btn-warning" name="submit" value="確定訂房">
                </div>
            </form>
        </div>

        <?php
            if(isset($_POST["submit"])){
                $row=query("SELECT*FROM `bookroom` WHERE `date`=?",[date("Ymd")]);
                query("INSERT INTO `bookroom`VALUES(?,?,?,?,?,?,?)",[null,date("Ymd").padstart(count($row)+1,4),$_POST["startday"],$_POST["endday"],$_POST["room"],date("Ymd"),""]);
                ?><script>alert("新增成功");location.href="no.php?no=<?= date("Ymd").padstart(count($row)+1,4) ?>"</script><?php
            }
        ?>

        <script src="init.js"></script>
        <script src="bookroomsubmit.js"></script>
    </body>
</html>