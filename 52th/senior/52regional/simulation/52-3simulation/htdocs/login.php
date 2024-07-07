<?php
    include("link.php");
    if(isset($_GET["submit"])){
        $username=$_GET["username"];
        $code=$_GET["pass"];
        $user=mysqli_query($db,"SELECT*FROM `user` WHERE `username`='$username'");
        $admin=mysqli_query($db,"SELECT*FROM `admin` WHERE `username`='$username'");
        if(!isset($_SESSION["error"])){
            $_SESSION["error"]=0;
        }
        if($row=mysqli_fetch_row($user)){
            if($code==$row[2]){
                if(isset($_GET["vererror"])){
                    $_SESSION["error"]++;
                    if($_SESSION["error"]<3){
                        mysqli_query($db,"INSERT INTO `data`(`usernumber`, `username`, `password`, `name`, `permission`, `logintime`, `logouttime`, `move`, `movetime`) VALUES ('$row[4]','$username','$row[2]','$row[3]','一般使用者','','','驗證碼錯誤','$time')");
                        ?><script>alert("驗證碼有誤");location.href='index.php'</script><?php
                    }else{
                        mysqli_query($db,"INSERT INTO `data`(`usernumber`, `username`, `password`, `name`, `permission`, `logintime`, `logouttime`, `move`, `movetime`) VALUES ('$row[4]','$username','$row[2]','$row[3]','一般使用者','$time','null','登入失敗','$time')");
                        session_unset();
                        ?><script>alert("驗證碼有誤");location.href='usererror.php'</script><?php
                    }

                }else{
                    mysqli_query($db,"INSERT INTO `data`(`usernumber`, `username`, `password`, `name`, `permission`, `logintime`, `logouttime`, `move`, `movetime`) VALUES ('$row[4]','$username','$row[2]','$row[3]','一般使用者','$time','','登入成功','$time')");
                    session_unset();
                    $_SESSION["starttime"]="升冪";
                    $_SESSION["date"]=date("Y-m-d");
                    // ?><script>   alert("登入成功");location.href='user.php'</script><?php
            }
            }else{
                $_SESSION["error"]++;
                if($_SESSION["error"]<3){
                    mysqli_query($db,"INSERT INTO `data`(`usernumber`, `username`, `password`, `name`, `permission`, `logintime`, `logouttime`, `move`, `movetime`) VALUES ('$row[4]','$username','$row[2]','$row[3]','一般使用者','','','密碼錯誤','$time')");
                    ?><script>alert("密碼有誤");location.href='index.php'</script><?php
                }else{
                    mysqli_query($db,"INSERT INTO `data`(`usernumber`, `username`, `password`, `name`, `permission`, `logintime`, `logouttime`, `move`, `movetime`) VALUES ('$row[4]','$username','$row[2]','$row[3]','一般使用者','$time','null','登入失敗','$time')");
                    session_unset();
                    ?><script>alert("密碼有誤");location.href='usererror.php'</script><?php
                }
            }
        }elseif($row=mysqli_fetch_row($admin)){
            if($code==$row[2]){
                if(isset($_GET["vererror"])){
                    $_SESSION["error"]++;
                    if($_SESSION["error"]<3){
                        mysqli_query($db,"INSERT INTO `data`(`usernumber`, `username`, `password`, `name`, `permission`, `logintime`, `logouttime`, `move`, `movetime`) VALUES ('$row[4]','$username','$row[2]','$row[3]','管理者','','','驗證碼錯誤','$time')");
                        ?><script>alert("驗證碼有誤");location.href='index.php'</script><?php
                    }else{
                        mysqli_query($db,"INSERT INTO `data`(`usernumber`, `username`, `password`, `name`, `permission`, `logintime`, `logouttime`, `move`, `movetime`) VALUES ('$row[4]','$username','$row[2]','$row[3]','管理者','$time','null','登入失敗','$time')");
                        session_unset();
                        ?><script>alert("驗證碼有誤");location.href='usererror.php'</script><?php
                    }
                }else{
                    mysqli_query($db,"INSERT INTO `data`(`usernumber`, `username`, `password`, `name`, `permission`, `logintime`, `logouttime`, `move`, `movetime`) VALUES ('$row[4]','$username','$row[2]','$row[3]','管理者','$time','','登入成功','$time')");
                    session_unset();
                    ?><script>alert("登入成功");location.href='admin.php'</script><?php
                }
            }else{
                $_SESSION["error"]++;
                if($_SESSION["error"]<3){
                    mysqli_query($db,"INSERT INTO `data`(`usernumber`, `username`, `password`, `name`, `permission`, `logintime`, `logouttime`, `move`, `movetime`) VALUES ('$row[4]','$username','$row[2]','$row[3]','管理者','','','密碼錯誤','$time')");
                    ?><script>alert("密碼有誤");location.href='index.php'</script><?php
                    
                }else{
                    mysqli_query($db,"INSERT INTO `data`(`usernumber`, `username`, `password`, `name`, `permission`, `logintime`, `logouttime`, `move`, `movetime`) VALUES ('$row[4]','$username','$row[2]','$row[3]','管理者','$time','null','登入失敗','$time')");
                    ?><script>alert("密碼有誤");location.href='usererror.php'</script><?php
                    session_unset();
                }
            }
        }else{
            $_SESSION["error"]++;
            if($_SESSION["error"]<3){
                ?><script>alert("帳號有誤");location.href='index.php'</script><?php
            }else{
                session_unset();
                ?><script>alert("帳號有誤");location.href='usererror.php'</script><?php
            }
        }
    }