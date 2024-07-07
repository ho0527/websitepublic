<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>TODO工作管理系統</title>
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <form>
            <h1>TODO工作管理系統</h1>
            <h3>
                帳號: <input type="text" name="username"><br>
                密碼: <input type="password" name="password"><br>
                <?php
                    include("login.php");
                ?>
            </h3><br>
            <h6>
                <h2>驗證碼</h2>
                <?php
                    for($i=0;$i<3;$i=$i+1){
                        $str=range("a","z");
                        $finalstr=$str[rand(0,25)]
                        ?>
                        <div class="dragbox">
                            <img class="drag<?= $i ?>" id="<?= $finalstr ?>" src="verifycode.php?val=<?= $finalstr ?>"></img>
                        </div>
                        <?php
                    }
                ?><button onclick="location.href='index.php'">重整驗證碼</button><br><br>
                請拖動驗證碼
                <?php
                    $key=rand(0,1);
                    $bos=["'由大排到小'","'由小排到大'"];
                    echo($bos[$key]);
                ?><br>
                <div class="dropbox" id="dropbox"></div>
            </h6><br><br>
            <input type="button" onclick="location.href='index.php'" value="清除">
            <input type="submit" value="登入">
            <input type="button" onclick="location.href='signup.php'" value="註冊">
        </form>
        <script src="verifycode.js"></script>
    </body>
</html>