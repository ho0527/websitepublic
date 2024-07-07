<?php
    include("link.php");
    $username=$_GET["username"];
    $password=$_GET["password"];
    $verifyans=$_GET["verifyans"];
    $ans=$_GET["ans"];

    if(!isset($_SESSION["error"])){ $_SESSION["error"]=0; }


    if($row=query($db,"SELECT*FROM `user` WHERE `username`=?",[$username])){
        if($password==$row[0][2]){
            if($verifyans==$ans){
                query($db,"INSERT INTO `data`(`number`, `username`, `name`, `move1`, `move2`, `time`) VALUES (?,?,?,'登入','成功','$time')",[
                    $row[0][4],$row[0][1],$row[0][3]
                ]);
                session_unset();
                $_SESSION["data"]=$row[0][0];
                $_SESSION["permission"]=$row[0][5];
                $_SESSION["timer"]=30;
                ?><script>alert("登入成功");location.href="verify.php"</script><?php
            }else{
                $_SESSION["error"]=$_SESSION["error"]+1;
                if($_SESSION["error"]>=3){
                    query($db,"INSERT INTO `data`(`number`, `username`, `name`, `move1`, `move2`, `time`) VALUES (?,?,?,'登入','失敗','$time')",[
                        $row[0][4],$row[0][1],$row[0][3]
                    ]);
                    session_unset();
                    ?><script>alert("驗證碼有誤");location.href="usererror.php"</script><?php
                }else{
                    ?><script>alert("驗證碼有誤");location.href="index.php"</script><?php
                }
            }
        }else{
            $_SESSION["error"]=$_SESSION["error"]+1;
            if($_SESSION["error"]>=3){
                query($db,"INSERT INTO `data`(`number`, `username`, `name`, `move1`, `move2`, `time`) VALUES (?,?,?,'登入','失敗','$time')",[
                    $row[0][4],$row[0][1],$row[0][3]
                ]);
                session_unset();
                ?><script>alert("密碼有誤");location.href="usererror.php"</script><?php
            }else{
                ?><script>alert("密碼有誤");location.href="index.php"</script><?php
            }
        }
    }else{
        $_SESSION["error"]=$_SESSION["error"]+1;
        if($_SESSION["error"]>=3){
            query($db,"INSERT INTO `data`(`number`, `username`, `name`, `move1`, `move2`, `time`) VALUES ('未知','','','登入','失敗','$time')");
            session_unset();
            ?><script>alert("帳號有誤");location.href="usererror.php"</script><?php
        }else{
            ?><script>alert("帳號有誤");location.href="index.php"</script><?php
        }
    }



?>