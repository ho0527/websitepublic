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
            if(!isset($_SESSION["signin"])){ header("location:signin.php"); }
        ?>
        <div class="nav" id="nav">
            <div class="title">訪客留言版</div>
        </div>
        <div class="nav2">
            <div class="title2">訪客留言列表</div>
            <input type="button" class="btn btn-warning" onclick="location.href='newc.php'" value="aaa">
        </div>

        <div class="main">
            <table class="table">
                <tr>
                    <th>#</th>
                    <th>name</th>
                    <th>price</th>
                    <th>fucn</th>
                </tr>
                <tr>
                    <td>1</td>
                    <td>name</td>
                    <td>1</td>
                    <td><input type="number" id="data" min="0" step="1" value="0"></td>
                </tr>
            </table>
            <div class="orderright" style="width: 20%;">
                <div>
                    總價格: $<span id="total">0</span>
                </div><br>
                <input type="button" class="btn btn-warning fill" onclick="if(confirm('test*'+document.getElementById('data').value)){ location.reload() }" value="確認" value="0">
            </div>
        </div>

        <script src="init.js"></script>
        <script src="orderfood.js"></script>
    </body>
</html>