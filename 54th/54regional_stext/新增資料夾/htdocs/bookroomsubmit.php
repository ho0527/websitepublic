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
            <div class="title">快樂旅遊網</div>
        </div>
        <div class="nav2 tc">
            <div class="title2">訪客訂房 - <span id="titletext">填寫聯絡方式</span></div>
        </div>

        <div class="main">
            <form method="POST" id="main">
                <p>姓名<input type="text" class="fill" name="username" required></p>
                <p>E-mail<input type="text" class="fill" name="email" required></p>
                <p>電話<input type="text" class="fill" name="phone" required></p>
                <p>備註<textarea name="ps" required></textarea></p>
                <div class="textcenter">
                    <input type="reset" class="btn btn-light" id="close" value="重設">
                    <input type="submit" class="btn btn-warning" name="roomsubmit" value="送出">
                </div>
            </form>
        </div>

        <?php
            if(isset($_POST["roomsubmit"])){
                $row=query($db,"SELECT*FROM `roomorder` WHERE `createdate`=?",[date("Ymd")]);
                $no=date("Ymd").str_pad((string)(count($row)+1),4,"0",STR_PAD_LEFT);
                query($db,"INSERT INTO `roomorder`(`no`,`startdate`,`enddate`,`roomno`,`username`,`phone`,`email`,`totalprice`,`deposit`,`ps`,`createtime`,`createdate`,`delete`)VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)",[$no,$_POST["startdate"],$_POST["enddate"],$_POST["roomno"],$_POST["username"],$_POST["phone"],$_POST["email"],$_POST["price"],$_POST["deposit"],$_POST["ps"],$time,date("Ymd"),"false"]);
                ?><script>location.href="successbookroom.php?no=<?= $no ?>"</script><?php
            }
        ?>

        <script src="init.js"></script>
        <script src="bookroomsubmit.js"></script>
    </body>
</html>