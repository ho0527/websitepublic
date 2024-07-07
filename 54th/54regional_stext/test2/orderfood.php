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
        <div class="nav" id="nav"></div>

        <div class="top100px">
            <table class="xcenter">
                <tr>
                    <th>#</th>
                    <th>名稱</th>
                    <th>價格</th>
                    <th>數量</th>
                </tr>
                <tr>
                    <td>1</td>
                    <td>名稱1</td>
                    <td>10</td>
                    <td><input type="number" class="number" data-price="10" min="0" step="1" value="0"></td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>名稱2</td>
                    <td>20</td>
                    <td><input type="number" class="number" data-price="20" min="0" step="1" value="0"></td>
                </tr>
            </table>
        </div>

        <div class="price">
            總價格: <span id="total">0</span>$<br>
            <input type="button" class="fill" onclick="alert('已送出');location.reload()" value="確認">
        </div>

        <script src="init.js"></script>
        <script src="orderfood.js"></script>
    </body>
</html>