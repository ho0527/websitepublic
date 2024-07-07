<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
        session_start();
        if(!isset($_SESSION["ok"])){ $_SESSION["ok"]=false; }
        if(!isset($_SESSION["msg"])){ $_SESSION["msg"]=""; }
        if($_SESSION["ok"]){
            header("location:main.php");
        }else{
            ?>
            <form action="check.php">
                <?php
                    echo($_SESSION["msg"]);
                ?><br><br>
                請輸入你的帳號 <input type="text" name="username"><br><br>
                請輸入你的密碼 <input type="text" name="password"><br><br>
                <input type="submit" value="登入">
            </form>
            <?php
        }
    ?>
</body>
</html>