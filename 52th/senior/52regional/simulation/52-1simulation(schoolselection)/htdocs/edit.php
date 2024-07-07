<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>amdin edit</title>
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <form>
            <?php
                include("link.php");
                if(isset($_GET["val"])){
                    $number=$_GET["val"];
                    $user=mysqli_query($db,"SELECT*FROM `user` WHERE `number`='$number'");
                    $admin=mysqli_query($db,"SELECT*FROM `admin` WHERE `number`='$number'");
                    if($row=mysqli_fetch_row($user)){
                        ?>
                        <h3>
                        編號: <input type="text" name="number" value="<?php echo($number); ?>" readonly><br>
                        帳號: <input type="text" name="username" value="<?php echo($row[2]); ?>"><br>
                        密碼: <input type="text" name="password" value="<?php echo($row[3]); ?>"><br>
                        名字: <input type="text" name="name" value="<?php echo($row[4]); ?>"><br><br>
                        <input type="submit" value="更改帳號" name="change">
                        <input type="submit" value="刪除帳號" name="del"><br><br>
                        <?php
                    }elseif($row=mysqli_fetch_row($admin)){
                        ?>
                        <h3>
                        編號: <input type="text" name="number" value="<?php echo($number); ?>" readonly><br>
                        帳號: <input type="text" name="username" value="<?php echo($row[2]); ?>"><br>
                        密碼: <input type="text" name="password" value="<?php echo($row[3]); ?>"><br>
                        名字: <input type="text" name="name" value="<?php echo($row[4]); ?>"><br><br>
                        <input type="submit" value="更改帳號" name="change">
                        <input type="submit" value="刪除帳號" name="del"><br><br>
                        <?php
                    }else{
                        echo("帳號已被刪除");
                    }
                }
            ?>
            <input type="button" onclick="location.href='adminwelcome.php'" value="返回">
        </form>
        <?php
            if(isset($_GET["change"])){
                $number=$_GET["number"];
                $username=$_GET["username"];
                $password=$_GET["password"];
                $name=$_GET["name"];
                $user=mysqli_query($db,"SELECT*FROM `user` WHERE `number`='$number'");
                $admin=mysqli_query($db,"SELECT*FROM `admin` WHERE `number`='$number'");
                $urow=mysqli_fetch_row($user);
                $arow=mysqli_fetch_row($admin);
                if($username==""||$password==""){
                    echo("請輸入帳密");
                }else{
                    if($urow){
                        mysqli_query($db,"UPDATE `user` SET `username`='$username',`password`='$password',`name`='$name'  WHERE `number`='$number'");
                        $row=mysqli_fetch_row(mysqli_query($db,"SELECT*FROM `user` WHERE `username`='$username'"));
                        mysqli_query($db,"INSERT INTO `data`(`number`, `username`, `password`, `name`, `permission`,`logintime`, `logouttime`, `move`, `movetime`) VALUES ('$row[1]','$row[2]','$row[3]','$row[4]','user','$time','','editbyadmin','$time')");
                        echo("更改成功");
                    }else{
                        mysqli_query($db,"UPDATE `admin` SET `username`='$username',`password`='$password',`name`='$name'  WHERE `number`='$number'");
                        $row=mysqli_fetch_row(mysqli_query($db,"SELECT*FROM `admin` WHERE `username`='$username'"));
                        mysqli_query($db,"INSERT INTO `data`(`number`, `username`, `password`, `name`, `permission`,`logintime`, `logouttime`, `move`, `movetime`) VALUES ('$row[1]','$row[2]','$row[3]','$row[4]','user','$time','','editbyadmin','$time')");
                        echo("更改成功");
                    }
                }
            }

            if(isset($_GET["del"])){
                $number=$_GET["numebr"];
                $username=$_GET["username"];
                $password=$_GET["password"];
                $name=$_GET["name"];
                $user=mysqli_query($db,"SELECT*FROM `user` WHERE `number`='$number'");
                $admin=mysqli_query($db,"SELECT*FROM `admin` WHERE `number`='$number'");
                $urow=mysqli_fetch_row($user);
                $arow=mysqli_fetch_row($admin);
                if($urow){
                    mysqli_query($db,"DELETE*FROM `user` WHERE `number`='$number'");
                    $row=mysqli_fetch_row(mysqli_query($db,"SELECT*FROM `user` WHERE `username`='$username'"));
                    mysqli_query($db,"INSERT INTO `data`(`number`, `username`, `password`, `name`, `permission`,`logintime`, `logouttime`, `move`, `movetime`) VALUES ('$row[1]','$row[2]','$row[3]','$row[4]','user','$time','','editbyadmin','$time')");
                    echo("刪除成功");
                }else{
                    mysqli_query($db,"DELETE*FROM `admin` WHERE `number`='$number'");
                    $row=mysqli_fetch_row(mysqli_query($db,"SELECT*FROM `admin` WHERE `username`='$username'"));
                    mysqli_query($db,"INSERT INTO `data`(`number`, `username`, `password`, `name`, `permission`,`logintime`, `logouttime`, `move`, `movetime`) VALUES ('$row[1]','$row[2]','$row[3]','$row[4]','user','$time','','editbyadmin','$time')");
                    echo("刪除成功");
                }
            }
        ?>
    </body>
</html>