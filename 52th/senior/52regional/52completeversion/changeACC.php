<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <link href="index.css" rel="Stylesheet">
        <title>重設帳密</title>
    </head>
    <body>
        <?php
            include("link.php");
            @$user_data=$_SESSION["data"];
            $row=mysqli_fetch_row(mysqli_query($db,"SELECT*FROM `user` WHERE `userNumber`='$user_data'"));
            if($row){
                ?>
                <form>
                    帳號id: <input type="text" name="number" value="<?php echo($user_data); ?>" readonly><br><br>
                    用戶帳號: <input type="text" name="username" value="<?php echo($row[1]); ?>"><br><br>
                    用戶名: <input type="text" name="name" value="<?php echo($row[3]); ?>"><br><br>
                    密碼: <input type="text" name="code" value="<?php echo($row[2]); ?>"><br><br>
                    <button name="enter">送出</button>
                </form>
                <?php
            }else{
                echo("請先登入");
            }
        ?><br>
        <button id="go_back" onclick="location.href='userWelcome.php'">返回主頁</button><br>
        <?php
            if(isset($_GET["enter"])){
                $username=$_GET["username"];
                $code=$_GET["code"];
                $name=$_GET["name"];
                $user=mysqli_query($db,"SELECT*FROM `user` WHERE `userNumber`='$user_data'");
                $data=mysqli_query($db,"SELECT*FROM `data` WHERE `usernumber`='$user_data'");
                if($row=mysqli_fetch_row($user)){
                    if($username!=""&&$code!=""){
                        mysqli_query($db,"UPDATE `user` SET `name`='$name',`userCode`='$code',`userName`='$username' WHERE `userNumber`='$user_data'");
                        $row=mysqli_fetch_row(mysqli_query($db,"SELECT*FROM `user` WHERE `userNumber`='$user_data'"));
                        mysqli_query($db,"INSERT INTO `data`(`usernumber`,`username`,`password`,`name`,`permission`,`logintime`,`logouttime`,`move`,`movetime`)
                        VALUES('$user_data','$row[1]','$row[2]','$row[3]','一般使用者','-','-','用戶編輯','$time')");
                        ?><script>alert("更改成功!");location.reload();</script><?php
                    }else{
                        echo("請填寫帳密");
                    }
                }
            }
        ?>
    </body>
</html>