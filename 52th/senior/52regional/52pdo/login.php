<?php
    include("link.php");
    if(isset($_GET["username"])){
        if(!isset($_SESSION["error"])){
            $_SESSION["error"]=0;
        }
        $username=$_GET['username'];
        $code=$_GET['code'];
        $_SESSION["username"]=$username;
        $_SESSION["password"]=$code;
        $user=query($db,"SELECT*FROM `user` WHERE `username`='$username'");
        $admin=query($db,"SELECT*FROM `admin` WHERE `adminName`='$username'");
        $location="index.php";
        if($row=query($db,"SELECT*FROM `user` WHERE `username`='$username'")){
            $row=$row[0];
            if($row[2]==$code){
                if(isset($_GET["vererror"])){
                    $_SESSION["error"]=$_SESSION["error"]+1;
                    if($_SESSION["error"]<3){
                        ?><script>alert("圖形驗證碼有誤");location.href="index.php"</script><?php
                    }else{
                        ?><script>alert("圖形驗證碼有誤");location.href="usererror.php"</script><?php
                        $login=query($db,"INSERT INTO `data`(`usernumber`,`username`,`password`,`name`,`permission`,`logintime`,`logouttime`,`move`,`movetime`)VALUES('$row[4]','$row[1]','$row[2]','$row[3]','一般使用者','$time','null','登入失敗','$time')");
                        session_unset();
                    }
                }else{
                    $login=query($db,"INSERT INTO `data`(`usernumber`,`username`,`password`,`name`,`permission`,`logintime`,`logouttime`,`move`,`movetime`)VALUES('$row[4]','$row[1]','$row[2]','$row[3]','一般使用者','$time','','登入成功','$time')");
                    session_unset();
                    $_SESSION["data"]=$row[4];
                    $_SESSION["date"]=date("Y-m-d");
                    $_SESSION["starttime"]="升冪";
                    ?><script>alert("登入成功");location.href="user.php"</script><?php
                }
            }else{
                $_SESSION["error"]=$_SESSION["error"]+1;
                if($_SESSION["error"]<3){
                    ?><script>alert("密碼有誤");location.href="index.php"</script><?php
                }else{
                    ?><script>alert("密碼有誤");location.href="usererror.php"</script><?php
                    $login=query($db,"INSERT INTO `data`(`usernumber`,`username`,`password`,`name`,`permission`,`logintime`,`logouttime`,`move`,`movetime`)VALUES('$row[4]','$row[1]','$row[2]','$row[3]','一般使用者','$time','null','登入失敗','$time')");
                    session_unset();
                }
            }
        }elseif($row=query($db,"SELECT*FROM `admin` WHERE `adminName`='$username'")){
            $row=$row[0];
            if($row[2]==$code){
                if(isset($_GET["vererror"])){
                    $_SESSION["error"]=$_SESSION["error"]+1;
                    if($_SESSION["error"]<3){
                        ?><script>alert("圖形驗證碼有誤");location.href="index.php"</script><?php
                    }else{
                        ?><script>alert("圖形驗證碼有誤");location.href="usererror.php"</script><?php
                        $login=query($db,"INSERT INTO `data`(`usernumber`,`username`,`password`,`name`,`permission`,`logintime`,`logouttime`,`move`,`movetime`)VALUES('$row[4]','$row[1]','$row[2]','$row[3]','管理者','$time','null','登入失敗','$time')");
                        session_unset();
                    }
                }else{
                    ?><script>alert("登入成功");location.href="admin.php"</script><?php
                    $login=query($db,"INSERT INTO `data`(`usernumber`,`username`,`password`,`name`,`permission`,`logintime`,`logouttime`,`move`,`movetime`)VALUES('$row[4]','$row[1]','$row[2]','$row[3]','管理者','$time','','登入成功','$time')");
                    session_unset();
                    $_SESSION["data"]=$row[4];
                }
            }else{
                $_SESSION["error"]=$_SESSION["error"]+1;
                if($_SESSION["error"]<3){
                    ?><script>alert("密碼有誤");location.href="index.php"</script><?php
                }else{
                    ?><script>alert("密碼有誤");location.href="usererror.php"</script><?php
                    $login=query($db,"INSERT INTO `data`(`usernumber`,`username`,`password`,`name`,`permission`,`logintime`,`logouttime`,`move`,`movetime`)VALUES('$row[4]','$row[1]','$row[2]','$row[3]','管理者','$time','null','登入失敗','$time')");
                    session_unset();
                }
            }
        }else{
            if($_SESSION["error"]<3){
                ?><script>alert("帳號有誤");location.href="index.php"</script><?php
                $_SESSION["error"]=$_SESSION["error"]+1;
            }else{
                ?><script>alert("帳號有誤");location.href="usererror.php"</script><?php
                session_unset();
            }
        }
    }else{
        ?><script>alert("未知錯誤請重新登入");location.href="index.php"</script><?php
    }
?>