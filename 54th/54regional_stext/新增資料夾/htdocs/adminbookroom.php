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
            if(!isset($_SESSION["islogin"])){ header("location: signin.php"); }
        ?>
        <div class="nav" id="adminnav">
            <div class="title">網站管理 - 網站管理</div>
        </div>
        <div class="nav2 textcenter">
            <div class="btn-group">
                <input type="button" class="btn btn-outline-light" onclick="location.href='admincomment.php'" value="留言管理">
                <input type="button" class="btn btn-outline-light active" onclick="location.href='adminbookroom.php'" value="訂房管理">
                <input type="button" class="btn btn-outline-light" onclick="location.href='adminorderfood.php'" value="訂餐管理">
                <input type="button" class="btn btn-outline-light" onclick="location.href='adminfood.php'" value="餐點管理">
            </div>
        </div>

        <div class="adminbookroommain">
            <table class="table table-hover textcenter">
                <tr>
                    <th>訂房編號</th>
                    <th>入住日期</th>
                    <th>房間編號</th>
                    <th>姓名</th>
                    <th>電話</th>
                    <th>E-mail</th>
                    <th>備註</th>
                    <th>功能區</th>
                </tr>
                <?php
                    $row=query($db,"SELECT*FROM `roomorder` WHERE `delete`='false'");
                    for($i=0;$i<count($row);$i=$i+1){
                        ?>
                        <tr>
                            <td><?= $row[$i][1] ?></td>
                            <td><?= $row[$i][2] ?>~<?= $row[$i][3] ?></td>
                            <td><?= $row[$i][4] ?></td>
                            <td><?= $row[$i][5] ?></td>
                            <td><?= $row[$i][6] ?></td>
                            <td><?= $row[$i][7] ?></td>
                            <td><?= $row[$i][10] ?></td>
                            <td>
                                <div class="btn-group">
                                    <input type="button" class="btn btn-outline-dark" onclick="location.href=''" value="修改">
                                    <input type="button" class="btn btn-outline-danger" onclick="location.href='api.php?deleteroomorder=<?= $row[$i][0] ?>'" value="刪除">
                                </div>
                            </td>
                        </tr>
                        <?php
                    }
                ?>
            </table>
        </div>

        <div class="lightbox" id="lightbox"></div>

        <script src="init.js"></script>
    </body>
</html>