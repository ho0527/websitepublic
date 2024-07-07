<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <div class="nbar">
            <div class="nbarlogo">logo</div>
            <div class="nbartitle">咖啡商品展示系統</div>
        </div>
        <div class="main">
            帳號: <input type="text" id="username"><br><br>
            密碼: <input type="text" id="password"><br><br>
            驗證碼
            <div id="verifycode"></div>
            <div class="bigorsmall" id="bigorsmall"></div>
            <div class="dropbox" id="drop"></div>
        </div>
        <input type="button" onclick="ref()" value="重新產生">
        <input type="button" onclick="cacle()" value="重設">
        <input type="button" onclick="login()" value="送出">
        <script src="index.js"></script>
    </body>
</html>