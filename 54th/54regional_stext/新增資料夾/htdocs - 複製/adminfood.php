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
        <div class="nav tr" id="adminnav">
            <div class="title">網站管理 - 餐點管理</div>
        </div>
        <div class="nav2 tc">
            <div class="btn-group">
                <input type="button" class="btn btn-outline-light" onclick="location.href='admincomment.php'" value="留言管理">
                <input type="button" class="btn btn-outline-light" onclick="location.href='adminbookroom.php'" value="訂房管理">
                <input type="button" class="btn btn-outline-light" onclick="location.href='adminorderfood.php'" value="訂餐管理">
                <input type="button" class="btn btn-outline-light active" onclick="location.href='adminfood.php'" value="餐點管理">
                <input type="button" class="btn btn-outline-light" onclick="location.href='newfood.php'" value="新增餐點">
            </div>
        </div>

        <div class="adminbookroommain">
            <table class="table textcenter">
                <tr>
                    <th>#</th>
                    <th>名稱</th>
                    <th>價格</th>
                    <th>功能區</th>
                </tr>
                <?php
                    $row=query($db,"SELECT*FROM `food`");
                    for($i=0;$i<count($row);$i=$i+1){
                        ?>
                        <tr>
                            <td><?= $row[$i][0] ?></td>
                            <td><?= $row[$i][1] ?></td>
                            <td><?= $row[$i][2] ?></td>
                            <td>
                                <input type="button" class="btn btn-outline-dark" onclick="location.href='editfood.php?id=<?= $row[$i][0] ?>'" value="修改">
                                <input type="button" class="btn btn-outline-danger" onclick="location.href='api.php?deletefood=<?= $row[$i][0] ?>'" value="刪除">
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