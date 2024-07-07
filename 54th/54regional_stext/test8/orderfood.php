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
            <div class="title">訪客訂餐</div>
        </div>

        <form action="?submit=" method="POST" id="form">
            <div class="maindiv">
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
                        <td><input type="number" class="text-center" name="data" id="data" value="0"></td>
                    </tr>
                </table>
    
                <div class="orderfodright">
                    總價格: $<span id="totalprice">0</span>
                    <input type="button" class="btn btn-warning fill" onclick="if(confirm('name*'+parseInt(document.getElementById('data').value)+'\n總價格: '+parseInt(document.getElementById('totalprice').innerHTML))) document.getElementById('form').submit()" value="確認">
                </div>
            </div>
        </form>

        <?php
            if(isset($_GET["submit"])){
                query("INSERT INTO `foodorder` VALUES(?,?,?)",[null,"name*".$_POST["data"],$_POST["data"]]);
                ?><script>alert("上傳成功");location.href="orderfood.php"</script><?php
            }
        ?>

        <script src="init.js"></script>
        <script src="orderfood.js"></script>
    </body>
</html>