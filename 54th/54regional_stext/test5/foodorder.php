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
        ?>
        <div class="nav" id="nav">
            <div class="title">訂餐管理</div>
        </div>

        <div class="main">
            <table class="table">
                <tr>
                    <th>#</th>
                    <th>name</th>
                    <th>price</th>
                    <th>count</th>
                </tr>
                <tr>
                    <td>1</td>
                    <td>name</td>
                    <td>1</td>
                    <td><input type="number" class="text-center" id="data" value="0"></td>
                </tr>
            </table>
            <div class="foodright">
                <div>總價格: $<span id="totalprice">0</span></div>
                <input type="button" class="btn btn-warning fill" onclick="if(confirm('name*'+document.getElementById('data').valuea)){ location.reload() }" value="確認">
            </div>
        </div>

        <script src="init.js"></script>
        <script src="foodorder.js"></script>
    </body>
</html>