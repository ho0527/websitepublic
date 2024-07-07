<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <input type="button" onclick="location.href='main.php'" value="首頁">
        <input type="button" onclick="location.href='productindex.php'" value="上架商品">
        <input type="button" onclick="location.href='admin.php'" value="會員管理">
        <input type="button" onclick="location.href='api.php?logout='" value="登出"><br><br>
        
        <input type="button" onclick="location.href='newproduct.php'" value="新增版型">
        <input type="button" onclick="location.href='admin.php'" value="選擇版型">
        <input type="button" onclick="location.href='admin.php'" value="填寫資料">
        <input type="button" onclick="location.href='admin.php'" value="預覽">
        <input type="button" onclick="location.href='admin.php'" value="確定送出">

        <div id="main"></div>
    </body>
</html>