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
            if(!isset($_SESSION["islogin"])){ header("location: signup.php"); }
        ?>
        <div class="nav tr" id="nav"></div>
        <div class="nav2 tc">
            <input type="button" class="btn-outline-primary" onclick="location.href='admincomment.php'" value="留言管理">
            <input type="button" class="btn-outline-primary active" onclick="location.href='adminbookroom.php'" value="訂房管理">
            <input type="button" class="btn-outline-primary" onclick="location.href='adminorderfood.php'" value="訂餐管理">
            <input type="button" class="btn-outline-primary" onclick="location.href='adminfood.php'" value="餐點管理">
        </div>

        <div class="commentmain">
            <table class="table">
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
                    <th>function</th>
                </tr>
                <script>
                    if(!localStorage.getItem('delete')){
                        document.write(`
                        <tr>
                            <td>202402020001</td>
                            <td>2024/02/02</td>
                            <td>2024/02/02</td>
                            <td>Rooom1</td>
                            <td>name</td>
                            <td>0912345789</td>
                            <td>email@mail.com</td>
                            <td>ps</td>
                            <td>5000</td>
                            <td>1500</td>
                            <td>
                                <input type="button" onclick="localStorage.setItem('delete','true');location.reload()" value="刪除">
                            </td>
                        </tr>
                        `)
                    }
                </script>
            </table>
        </div>
        <script src="init.js"></script>
    </body>
</html>