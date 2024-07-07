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
            if(!isset($_SESSION["issignin"])){ header("location: signin.php"); }
        ?>
        <div class="nav tr" id="">
            <div></div>
            <div class="navdiv">
                <input type="button" class="btn-primary" id="index.php" onclick="location.href='index.php'" value="首頁">
                <input type="button" class="btn-primary" id="comment.php" onclick="location.href='comment.php'" value="訪客留言">
                <input type="button" class="btn-primary" id="bookroom.php" onclick="location.href='bookroom.php'" value="訪客訂房">
                <input type="button" class="btn-primary" id="orderfood.php" onclick="location.href='orderfood.php'" value="訪客訂餐">
                <input type="button" class="btn-primary" id="info.php" onclick="location.href='info.php'" value="交通資訊">
                <input type="button" class="btn-primary" id="signin.php" onclick="location.href='api.php?signout='" value="登出">
            </div>
        </div>
        <div class="nav2 tr" id="">
            <input type="button" class="btn-outline-primary" id="admincomment.php" onclick="location.href='admincomment.php'" value="首頁">
            <input type="button" class="btn-outline-primary active" id="adminbookroom.php" onclick="location.href='adminbookroom.php'" value="訂房管理">
            <input type="button" class="btn-outline-primary" id="admin.php" onclick="location.href='adminorderfood.php'" value="訂餐管理">
        </div>

        <div class="top100px">
            <table class="xcenter tc">
                <tr>
                    <th>訂房編號</th>
                    <th>入住第一晚的日期</th>
                    <th>入住最後一晚的日期</th>
                    <th>房間編號</th>
                    <th>姓名</th>
                    <th>電話</th>
                    <th>E-mail</th>
                    <th>備註</th>
                    <th>總金額</th>
                    <th>需付訂金</th>
                    <th>功能區</th>
                </tr>
                <?php
                    if(!isset($_SESSION["key1"])){
                        ?>
                        <tr>
                            <td>202402060001</td>
                            <td colspan="2">2024/02/20~2024/02/20</td>
                            <td>Room8</td>
                            <td>123</td>
                            <td>456</td>
                            <td>123456789</td>
                            <td>132465789</td>
                            <td>5000</td>
                            <td>1500</td>
                            <td><input type="button" onclick="location.href='api.php?deleteroom=1'" value="刪除"></td>
                        </tr>
                        <?php
                    }
                    if(!isset($_SESSION["key2"])){
                        ?>
                        <tr>
                            <td>202402060002</td>
                            <td colspan="2">2024/02/10~2024/02/10</td>
                            <td>Room1、2、3、4、5、6、7、8</td>
                            <td>123</td>
                            <td>456</td>
                            <td>123456789</td>
                            <td>132465789</td>
                            <td>40000</td>
                            <td>12000</td>
                            <td><input type="button" onclick="location.href='api.php?deleteroom=2'" value="刪除"></td>
                        </tr>
                        <?php
                    }
                    if(!isset($_SESSION["key3"])){
                        ?>
                        <tr>
                            <td>202402060003</td>
                            <td colspan="2">2024/02/06~2024/02/07</td>
                            <td>Room7</td>
                            <td>123</td>
                            <td>456</td>
                            <td>123456789</td>
                            <td>132465789</td>
                            <td>10000</td>
                            <td>3000</td>
                            <td><input type="button" onclick="location.href='api.php?deleteroom=3'" value="刪除"></td>
                        </tr>
                        <?php
                    }
                ?>
            </table>
        </div>

        <script src="init.js"></script>
    </body>
</html>