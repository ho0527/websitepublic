<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <form>
            <?php
                include("link.php");
                if(isset($_GET["val"])){
                    $val=$_GET["val"];
                    $user=mysqli_query($db,"SELECT*FROM `user` WHERE `usernumber`='$val'");
                    $auser=mysqli_query($db,"SELECT*FROM `admin` WHERE `usernumber`='$val'");
                    if($row=mysqli_fetch_row($user)){
                        ?>
                        <h3>
                            帳號: <input type="text" name="usernumber" id="username" value=<?= $row[1] ?> readonly><br>
                            帳號: <input type="text" name="username" id="username" value=<?= $row[2] ?>><br>
                            密碼: <input type="text" name="password" id="password" value=<?= $row[3] ?>><br>
                            用戶名: <input type="text" name="name" id="password" value=<?= $row[4] ?>><br>
                            <input type="submit" name="enter" value="送出"><br>
                            <input type="button" onclick="location.href='adminWelcome.php'" value="返回">
                        </h3>
                        <?php
                    }elseif($row=mysqli_fetch_row($auser)){
                        ?>
                        <h3>
                            帳號: <input type="text" name="usernumber" id="username" value=<?= $row[1] ?> readonly><br>
                            帳號: <input type="text" name="username" id="username" value=<?= $row[2] ?>><br>
                            密碼: <input type="text" name="password" id="password" value=<?= $row[3] ?>><br>
                            用戶名: <input type="text" name="name" id="password" value=<?= $row[4] ?>><br>
                            <input type="submit" name="enter" value="送出"><br>
                            <input type="button" onclick="location.href='adminWelcome.php'" value="返回">
                        </h3>
                        <?php
                    }else{
                        ?>
                        <h3>
                            帳號已被刪除<br>
                            <input type="button" onclick="location.href='adminWelcome.php'" value="返回">
                        </h3>
                        <?php
                    }
                }
            ?>
        </form>
        <?php
            if(isset($_GET["enter"])){
                $usernumber=$_GET["usernumber"];
                $username=$_GET["username"];
                $password=$_GET["password"];
                $name=$_GET["name"];
                $udata=mysqli_fetch_row(mysqli_query($db,"SELECT*FROM `user` WHERE `usernumber`='$usernumber'"));
                $adata=mysqli_fetch_row(mysqli_query($db,"SELECT*FROM `admin` WHERE `usernumber`='$usernumber'"));
                if($username==""||$password==""){
                    echo("請填寫帳密");
                }else{
                    if($udata){
                        mysqli_query($db,"UPDATE `user` SET `username`='$name',`password`='$password',`name`='$name'");
                        $row=mysqli_fetch_row(mysqli_query($db,"SELECT*FROM `user` WHERE `usernumber`='$usernumber'"));
                        mysqli_query($db,"INSERT INTO `data`(`usernumber`, `username`, `password`, `name`,`permission`,`logintime`, `logouttime`, `move`, `movetime`)
                         VALUES('$row[1]','$row[2]','$row[3]','$row[4]','一般使用者','$date','','adminedit','$date')");
                        header("location:adminWelcome.php");
                    }else{
                        mysqli_query($db,"UPDATE `user` SET `username`='$name',`password`='$password',`name`='$name'");
                        $row=mysqli_fetch_row(mysqli_query($db,"SELECT*FROM `user` WHERE `usernumber`='$usernumber'"));
                        mysqli_query($db,"INSERT INTO `data`(`usernumber`, `username`, `password`, `name`,`permission`,`logintime`, `logouttime`, `move`, `movetime`)
                         VALUES('$row[1]','$row[2]','$row[3]','$row[4]','管理者','$date','','adminedit','$date')");
                        header("location:adminWelcome.php");
                    }
                }
            }
        ?>
    </body>
</html>