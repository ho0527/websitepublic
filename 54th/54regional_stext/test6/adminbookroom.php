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
            if(!isset($_SESSION["signin"])){ header("location: signin.php"); }
        ?>
        <div class="nav" id="adminnav">
            <div class="title">網站管理 - 訂房管理</div>
        </div>
        <div class="nav2">
            <div class="btn-group">
                <a href="admincomment.php" class="btn btn-outline-light">留言管理</a>
                <a href="adminbookroom.php" class="btn btn-outline-light">訂房管理</a>
                <a href="adminorderfood.php" class="btn btn-outline-light">訂餐管理</a>
            </div>
        </div>

        <div class="maindiv">
            <table class="orderfoodtable text-center">
                <tr>
                    <th>編號</th>
                    <th>日期</th>
                    <th>房號</th>
                    <th>總金額</th>
                    <th>功能區</th>
                </tr>
                <?php
                    $row=query("SELECT*FROM `bookroom` WHERE `delete`=''");
                    for($i=0;$i<count($row);$i=$i+1){
                        ?>
                        <tr>
                            <td><?= $row[$i]["number"] ?></td>
                            <td><?= $row[$i]["firstday"] ?>~<?= $row[$i]["lastday"] ?></td>
                            <td><?= $row[$i]["room"] ?></td>
                            <td><?= $row[$i]["totalprice"] ?></td>
                            <td>
                                <input type="button" class="btn btn-light" onclick="alert('nope')" value="修改">
                                <input type="button" class="btn btn-danger" onclick="if(confirm('confirm?')){ location.href='api.php?deleteroom=<?= $row[$i]['id'] ?>' }" value="刪除">
                            </td>
                        </tr>
                        <?php
                    }
                ?>
            </table>
        </div>

        <script src="init.js"></script>
    </body>
</html>