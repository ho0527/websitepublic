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
                密碼: <input type="text" name="password"><br>
                名字: <input type="text" name="name"><br>
                管理員權限: <input type="checkbox" name="admin"><br>
            <input type="submit" value="送出"><br><br>
            <input type="button" onclick="location.href='index.php'" value="返回">
        </form>
        <?php
            include("link.php");
            if(isset($_GET["username"])){
                $username=$_GET["username"];
                $password=$_GET["password"];
                $name=$_GET["name"];
                $user=mysqli_query($db,"SELECT*FROM `user` WHERE `username`='$username'");
                $admin=mysqli_query($db,"SELECT*FROM `admin` WHERE `username`='$username'");
                $urow=mysqli_fetch_row($user);
                $arow=mysqli_fetch_row($admin);
                if($username==""||$password==""){
                    echo("請輸入帳密");
                }elseif($urow||$arow){
                    ehco("帳號已被註冊");
                }else{
                    if(isset($_GET["admin"])){
                        mysqli_query($db,"INSERT INTO `admin`( `username`, `password`, `name`) VALUES ('$username','$password','$name')");
                        $row=mysqli_fetch_row(mysqli_query($db,"SELECT*FROM `admin` WHERE `username`='$username'"));
                        $number="a".str_pad($row[0],3,"0",STR_PAD_LEFT);
                        mysqli_query($db,"UPDATE `admin` SET `number`='$number' WHERE `username`='$username'");
                        $row=mysqli_fetch_row(mysqli_query($db,"SELECT*FROM `admin` WHERE `username`='$username'"));
                        mysqli_query($db,"INSERT INTO `data`(`number`, `username`, `password`, `name`, `permission`,`logintime`, `logouttime`, `move`, `movetime`) VALUES ('$row[1]','$row[2]','$row[3]','$row[4]','user','$time','','signup','$time')");
                        header("location: index.php");
                    }else{
                        mysqli_query($db,"INSERT INTO `user`( `username`, `password`, `name`) VALUES ('$username','$password','$name')");
                        $row=mysqli_fetch_row(mysqli_query($db,"SELECT*FROM `user` WHERE `username`='$username'"));
                        $number="u".str_pad($row[0],3,"0",STR_PAD_LEFT);
                        mysqli_query($db,"UPDATE `user` SET `number`='$number' WHERE `username`='$username'");
                        $row=mysqli_fetch_row(mysqli_query($db,"SELECT*FROM `user` WHERE `username`='$username'"));
                        mysqli_query($db,"INSERT INTO `data`(`number`, `username`, `password`, `name`, `permission`,`logintime`, `logouttime`, `move`, `movetime`) VALUES ('$row[1]','$row[2]','$row[3]','$row[4]','user','$time','','signup','$time')");
                        header("location: index.php");
                    }
                }
            }
        ?>
    </body>
</html>