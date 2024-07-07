<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <link rel="stylesheet" href="bootstrap.css">
        <link rel="stylesheet" href="style.css">
        <script src="jquery.js"></script>
        <script src="bootstrap.js"></script>
    </head>

    <body>
        <?php
            include("link.php");
            $site = $pdo->query("SELECT * FROM `site` WHERE 1")->fetchAll();
            $bus = $pdo->query("SELECT * FROM `bus` WHERE 1")->fetchAll();
            date_default_timezone_set("Asia/Taipei");
        ?>
        <div class="p-3 d-flex border-bottom">
            <img style="height: 60px;cursor: pointer;" onclick="location='index.php'" src="./picture/123456.png" alt="">
            <h1 class="mx-3 m-1 strong">南港展覽館接駁專車系統</h1>
            <h3 onclick="location='./login.html'" class="m-3 btn btn-outline-primary" style="position: absolute;right: 0%;">系統管理</h3>
        </div>
        <div style="position: absolute;top: 20%;left: 10%;font-size: 20px;">
            <p>現在時間:<?= date("H:i"); ?></p>
        </div>
        <p style="font-size: 15px;position: absolute;bottom:5%;left: 10%;" id="time">00秒前刷新</p>
        <div class="rounded" style="width: 1200px;height:600px;background-color:rgb(196, 196, 255);position: absolute;top: 25%;left: 20%;">
            <div class="d-flex m-0 space-around" style="position: absolute;top: 5%;left: 1%;">
                <?php
                foreach ($site as $name) {
                ?>
                    <?php
                    ?>
                    <div class="text-center">
                        <p><?= $name['site'] ?></p>
                        <img class="site" style="height: 40px;width:40px" src="./point.png" alt="">
                        <p style="border: solid black 5px; width: 100px;"></p>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
        <script>
            let time = $("#time");
            let seconds = 0;

            function update() {
                seconds++;
                let minutes = Math.floor(seconds / 60);
                let drop = seconds % 60;
                if (drop < 10) {
                    drop = '0' + drop;
                } else {
                    location.reload()
                }
                time.text(drop + '秒前刷新');
            }

            function reset() {
                seconds = 0;
            }

            reset();
            setInterval(update, 1000);
            setInterval(reset, 10000);
        </script>
    </body>
</html>