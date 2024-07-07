<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <?php
            include("link.php");
        ?>
        <div class="nbar">
            <img src="media/icon/mainicon.gif" class="logo">
            <h1 class="title">咖啡商品展示系統</h1>
        </div>
        <div class="main">
            <div class="verifytext" id="vtext"></div>
            <table>
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
                <tr>
            </table><br><br>
            <div class="buttondiv">
                <input type="button" onclick="location.href='api/api.php?logout='" value="登出">
                <input type="button" id="next" value="切換">
                <input type="button" id="submit" value="確定">
            </div>
        </div>
        <script src="verify.js"></script>
    </body>
</html>