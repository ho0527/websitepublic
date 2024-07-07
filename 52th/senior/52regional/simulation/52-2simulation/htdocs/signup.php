<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <link rel="stylesheet" href="index.css">
    </head>
    <body>
        <form>
            <h3>
                帳號: <input type="text" name="username" id="username"><br>
                密碼: <input type="text" name="password" id="password"><br>
                用戶名: <input type="text" name="name" id="password"><br>
                管理員權限: <input type="checkbox" name="admin" id="password"><br>
                <input type="submit" name="enter" value="送出"><br>
                <input type="button" name="enter" onclick="location.href='adminWelcome.php'" value="返回">
            </h3>
        </form>
        <?php
            include("link.php");
            if(isset($_GET["enter"])){
                $username=$_GET["username"];
                $password=$_GET["password"];
                $name=$_GET["name"];
                $udata=mysqli_fetch_row(mysqli_query($db,"SELECT*FROM `user` WHERE `username`='$username'"));
                $adata=mysqli_fetch_row(mysqli_query($db,"SELECT*FROM `admin` WHERE `username`='$username'"));
                if($adata||$udata){
                    echo("帳號已被註冊");
                }elseif($username==""||$password==""){
                    echo("請填寫帳密");
                }else{
                    if(isset($_GET["admin"])){
                        mysqli_query($db,"INSERT INTO `admin`(`username`, `password`, `name`) VALUES ('$username','$password','$name')");
                        $row=mysqli_fetch_row(mysqli_query($db,"SELECT*FROM `admin` WHERE `username`='$username'"));
                        $number="a".str_pad($row[0],3,"0",STR_PAD_LEFT);
                        mysqli_query($db,"UPDATE `user` SET `usernumber`='$number' WHERE `username`='$username' ");
                        $row=mysqli_fetch_row(mysqli_query($db,"SELECT*FROM `user` WHERE `username`='$username'"));
                        mysqli_query($db,"INSERT INTO `data`(`usernumber`, `username`, `password`, `name`,`permission`,`logintime`, `logouttime`, `move`, `movetime`)
                         VALUES('$row[1]','$row[2]','$row[3]','$row[4]','管理者','$date','','signup','$date')");
                        header("location:adminWelcome.php");
                    }else{
                        mysqli_query($db,"INSERT INTO `user`(`username`, `password`, `name`) VALUES ('$username','$password','$name')");
                        $row=mysqli_fetch_row(mysqli_query($db,"SELECT*FROM `user` WHERE `username`='$username'"));
                        $number="u".str_pad($row[0],3,"0",STR_PAD_LEFT);
                        mysqli_query($db,"UPDATE `user` SET `usernumber`='$number' WHERE `username`='$username' ");
                        $row=mysqli_fetch_row(mysqli_query($db,"SELECT*FROM `user` WHERE `username`='$username'"));
                        mysqli_query($db,"INSERT INTO `data`(`usernumber`, `username`, `password`, `name`,`permission`,`logintime`, `logouttime`, `move`, `movetime`)
                         VALUES('$row[1]','$row[2]','$row[3]','$row[4]','一般使用者','$date','','signup','$date')");
                        header("location:adminWelcome.php");
                    }
                }
            }
        ?>
    </body>
</html>