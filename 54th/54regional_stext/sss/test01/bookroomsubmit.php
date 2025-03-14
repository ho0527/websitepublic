<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1.0">
        <title>快樂旅遊網</title>
        <link rel="stylesheet" href="bootstrap.css">
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <?php include("link.php"); ?>
        <div class="nav" id="adminnav">
            <div class="title">訪客訂房 - 填寫聯絡資訊</div>
        </div>

        <div class="maindiv">
            <form method="POST" id="main">
                <div class="margin-5px0px">
                    name
                    <input type="text" name="name" required>
                </div>
                <div class="margin-5px0px">
                    phone
                    <input type="text" name="phone" required>
                </div>
                <div class="margin-5px0px">
                    Email
                    <input type="text" name="email" required>
                </div>
                <div class="margin-5px0px">
                    ps
                    <textarea name="ps" required></textarea>
                </div>
                <div class="text-center">
                    <input type="reset" class="btn btn-light" value="重設">
                    <input type="submit" class="btn btn-warning" name="submit" value="送出">
                </div>
            </form>
        </div>

        <?php
            if(isset($_POST["submit"])){
                $row=query("SELECT*FROM `bookroom` WHERE `date`=?",[date("Ymd")]);
                $no=date("Ymd").padstart(4,count($row)+1);
                query(
                    "INSERT INTO `bookroom`(`no`,`startday`,`endday`,`room`,`name`,`phone`,`email`,`ps`,`price`,`delete`,`date`)VALUES(?,?,?,?,?,?,?,?,?,?,?)",
                    [$no,$_POST["startday"],$_POST["endday"],$_POST["room"],$_POST["name"],$_POST["phone"],$_POST["email"],$_POST["ps"],$_POST["price"],"",date("Ymd")]
                );
                ?><script>location.href="no.php?no="+<?= $no; ?></script><?php
            }
        ?>

        <script src="init.js"></script>
        <script src="bookroomsubmit.js"></script>
    </body>
</html>