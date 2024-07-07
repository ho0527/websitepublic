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
        ?>
        <div class="nav tr" id="nav">
            <div class="title">訪客訂餐</div>
        </div>

        <div class="adminbookroommain">
            <table class="table orderfoodtable textcenter">
                <tr>
                    <th>#</th>
                    <th>name</th>
                    <th>price</th>
                    <th>數量</th>
                </tr>
                <?php
                    $row=query($db,"SELECT*FROM `food`");
                    for($i=0;$i<count($row);$i=$i+1){
                        ?>
                        <tr>
                            <td><?= $row[$i][0] ?></td>
                            <td><?= $row[$i][1] ?></td>
                            <td><?= $row[$i][2] ?></td>
                            <td><input type="number" class="number tc" data-price="<?= $row[$i][2] ?>" min="0" step="1" value="0"></td>
                        </tr>
                        <?php
                    }
                ?>
            </table>
            <div class="foodfunction">
                <p>總金額: <span class="tc" id="price">0</span>$</p>
                <p>
                    <input type="button" class="fill" onclick="alert('已送出');location.reload()" value="送出">
                </p>
            </div>
        </div>

        <script src="init.js"></script>
        <script src="orderfood.js"></script>
    </body>
</html>