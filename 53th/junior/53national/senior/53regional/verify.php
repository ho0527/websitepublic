<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>網站前台登入介面</title>
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <?php
            include("link.php");
            if(!isset($_SESSION["data"])){ header("location:index.php"); }
        ?>
        <div class="main">
            <h1>咖啡商品展示系統</h1><hr>
            <h2>第二層驗證</h2>
            <p>請點選方塊來連成水平或垂直線</p>
            <table class="verifytable">
                <tr>
                    <td class="verifytd"></td>
                    <td class="verifytd"></td>
                    <td class="verifytd"></td>
                </tr>
                <tr>
                    <td class="verifytd"></td>
                    <td class="verifytd"></td>
                    <td class="verifytd"></td>
                </tr>
                <tr>
                    <td class="verifytd"></td>
                    <td class="verifytd"></td>
                    <td class="verifytd"></td>
                </tr>
            </table><br>
            <input type="button" class="button" onclick="location.href='link.php?logout='" value="登出">
            <input type="button" class="button" onclick="location.reload()" value="重設">
            <input type="button" class="button" onclick="check()" value="確定">
        </div>
        <script src="verify.js"></script>
    </body>
</html>