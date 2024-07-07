<?php
    include("link.php");
    if(!isset($_SESSION["error"])){
        $_SESSION["error"]=0;
    }
    if(isset($_GET["vererror"])){
        $_SESSION["error"]=$_SESSION["error"]+1;
        if($_SESSION["error"]<3){
            ?><script>alert("圖形驗證碼有誤");location.href="index.php"</script><?php
        }else{
            ?><script>alert("圖形驗證碼有誤");location.href="usererror.php"</script><?php
            unset($_SESSION["error"]);
        }
    }
    if(isset($_GET["username"])){
        $username=$_GET["username"];
        $password=$_GET["password"];
        $user=mysqli_query($db,"SELECT*FROM `user` WHERE `username`='$username'");
        $admin=mysqli_query($db,"SELECT*FROM `admin` WHERE `username`='$username'");
        if($row=mysqli_fetch_row($user)){
            if($row[3]==$password){
                ?><script>alert("登入成功");location.href="userWelcome.php"</script><?php
                mysqli_query($db,"INSERT INTO `data`(`usernumber`, `username`, `password`, `name`,`permission`,`logintime`, `logouttime`, `move`, `movetime`)
                 VALUES('$row[1]','$row[2]','$row[3]','$row[4]','一般使用者','$date','','登入成功','$date')");
                $_SESSION["userdata"]=$username;
                $_SESSION["date"]=$date;
                unset($_SESSION["error"]);
            }else{
                $_SESSION["error"]=$_SESSION["error"]+1;
                if($_SESSION["error"]<3){
                    ?><script>alert("密碼有誤");location.href="index.php"</script><?php
                }else{
                    ?><script>alert("密碼有誤");location.href="usererror.php"</script><?php
                    mysqli_query($db,"INSERT INTO `data`(`usernumber`, `username`, `password`, `name`,`permission`,`logintime`, `logouttime`, `move`, `movetime`)
                     VALUES('$row[1]','$row[2]','$row[3]','$row[4]','一般使用者','$date','null','登入失敗','$date')");
                    unset($_SESSION["error"]);
                }
            }
        }elseif($row=mysqli_fetch_row($admin)){
            if($row[3]==$password){
                ?><script>alert("登入成功");location.href="adminWelcome.php"</script><?php
                mysqli_query($db,"INSERT INTO `data`(`usernumber`, `username`, `password`, `name`,`permission`,`logintime`, `logouttime`, `move`, `movetime`) 
                VALUES('$row[1]','$row[2]','$row[3]','$row[4]','管理者','$date','','登入成功','$date')");
                $_SESSION["admindata"]=$username;
            }else{
                $_SESSION["error"]=$_SESSION["error"]+1;
                if($_SESSION["error"]<3){
                    ?><script>alert("密碼有誤");location.href="index.php"</script><?php
                }else{
                    ?><script>alert("密碼有誤");location.href="usererror.php"</script><?php
                    mysqli_query($db,"INSERT INTO `data`(`usernumber`, `username`, `password`, `name`,`permission`,`logintime`, `logouttime`, `move`, `movetime`)
                    VALUES('$row[1]','$row[2]','$row[3]','$row[4]','管理者','$date','null','登入失敗','$date')");
                    unset($_SESSION["error"]);
                }
            }
        }else{
            $_SESSION["error"]=$_SESSION["error"]+1;
            if($_SESSION["error"]<3){
                ?><script>alert("帳號有誤");location.href="index.php"</script><?php
            }else{
                ?><script>alert("帳號有誤");location.href="usererror.php"</script><?php
                unset($_SESSION["error"]);
            }
        }
    }
?>