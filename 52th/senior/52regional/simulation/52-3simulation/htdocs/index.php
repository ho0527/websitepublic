<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>網站前台登入介面</title>
    <link rel="stylesheet" href="index.css">
</head>
<body style="text-align:left">
    <img src="img9.jpg" style="width:600px">
    <h1>
    網站前台登入介面<br>
    TODO工作管理系統
    </h1>
    <form>
        <?php include("link.php") ?>
        帳號: <input type="text" id="user" value="<?= @$_SESSION["user"] ?>" name="username"><br>
        密碼: <input type="text" id="code" value="<?= @$_SESSION["pass"] ?>" name="pass"><br>
        驗證碼:<br>
        <?php
            for($i=0;$i<3;$i=$i+1)
        {
            $str=range("a","z")[rand(0,25)]
            ?>
                <div class="dragbox">
                    <img src="verifycode.php?val=<?= $str ?>" id=<?= $str ?> class="drag">
                </div>
            <?php
        }
        ?><input type="button" value="重整圖片" onclick="location.href='index.php'"><br>
        請託動驗證碼
        <?php
        $bos=[
            "'由大排到小'",
            "'由小排到大'",
        ];
        $key=rand(0,1);
        echo($bos[$key]);
        ?><br>
        <div id="dropbox"></div>
        <input type="button" value="清除" onclick="location.href='index.php'">
        <input type="button" value="登入" onclick="login(<?= $key ?>)" name="submit">
    </form>
    <script src="verifycode.js"></script>
</body>
</html>