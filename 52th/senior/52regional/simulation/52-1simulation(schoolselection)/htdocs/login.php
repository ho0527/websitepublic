<?php
    include("link.php");
    if(isset($_GET["username"])){
        $username=$_GET["username"];
        $password=$_GET["password"];
        $user=mysqli_query($db,"SELECT*FROM `user` WHERE `username`='$username'");
        $admin=mysqli_query($db,"SELECT*FROM `admin` WHERE `username`='$username'");
        if(!isset($_SESSION["error"])){
            $_SESSION["error"]=0;
        }
        if($row=mysqli_fetch_row($user)){
            if($row[3]==$password){
                mysqli_query($db,"INSERT INTO `data`(`number`, `username`, `password`, `name`, `permission`,`logintime`, `logouttime`, `move`, `movetime`) VALUES ('$row[1]','$row[2]','$row[3]','$row[4]','user','$time','','login','$time')");
                header("location: userwelcome.php");
                session_unset();
            }else{
                if($_SESSION["error"]<3){
                    echo("密碼輸入錯誤");
                    $_SESSION["error"]=$_SESSION["error"]+1;
                }else{
                    header("location:usererror.php");
                    mysqli_query($db,"INSERT INTO `data`(`number`, `username`, `password`, `name`, `permission`,`logintime`, `logouttime`, `move`, `movetime`) VALUES ('$row[1]','$row[2]','$row[3]','$row[4]','user','$time','null','login','$time')");
                    session_unset();
                }
            }
        }elseif($row=mysqli_fetch_row($admin)){
            if($row[3]==$password){
                mysqli_query($db,"INSERT INTO `data`(`number`, `username`, `password`, `name`, `permission`,`logintime`, `logouttime`, `move`, `movetime`) VALUES ('$row[1]','$row[2]','$row[3]','$row[4]','admin','$time','','login','$time')");
                header("location: adminwelcome.php");
                session_unset();
            }else{
                if($_SESSION["error"]<3){
                    echo("密碼輸入錯誤");
                    $_SESSION["error"]=$_SESSION["error"]+1;
                }else{
                    header("location:usererror.php");
                    mysqli_query($db,"INSERT INTO `data`(`number`, `username`, `password`, `name`, `permission`,`logintime`, `logouttime`, `move`, `movetime`) VALUES ('$row[1]','$row[2]','$row[3]','$row[4]','admin','$time','null','login','$time')");
                    session_unset();
                }
            }
        }else{
            if($_SESSION["error"]<3){
                echo("帳號輸入錯誤");
                $_SESSION["error"]=$_SESSION["error"]+1;
            }else{
                header("location:usererror.php");
                session_unset();
            }
        }
    }
?>