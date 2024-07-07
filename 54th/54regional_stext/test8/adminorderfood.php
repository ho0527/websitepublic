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
            <div class="title">網站管理 - 留言管理</div>
        </div>
        <div class="nav2" id="adminnav">
            <div class="btn-group">
                <a href="admincomment.php" class="btn btn-outline-light">留言管理</a>
                <a href="adminbookroom.php" class="btn btn-outline-light">訂房管理</a>
                <a href="adminorderfood.php" class="btn btn-outline-light">訂餐管理</a>
            </div>
        </div>

        <div class="maindiv adminbookroomdiv">
            <table class="adminbookroomtable table text-center">
                <tr class="position-sticky">
                    <th>#</th>
                    <th>訂購資料</th>
                    <th>總金額</th>
                    <th>功能區</th>
                </tr>
                <?php
                    $row=query("SELECT*FROM `foodorder`");
                    for($i=0;$i<count($row);$i=$i+1){
                        ?>
                        <tr>
                            <td><?= $row[$i]["id"] ?></td>
                            <td><?= $row[$i]["data"] ?></td>
                            <td class="text-right">$<?= $row[$i]["totalprice"] ?></td>
                            <td>
                                <input type="button" class="btn btn-light" onclick="alert('nope?!')" value="修改">
                                <input type="button" class="btn btn-danger" onclick="if(confirm('confirm?')) location.href='api.php?deletefoodorder=<?= $row[$i]['id'] ?>'" value="刪除">
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