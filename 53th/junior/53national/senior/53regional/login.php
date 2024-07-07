<?php
    include("link.php");
    if(isset($_GET["username"])){
        if(!isset($_SESSION["error"])){
            $_SESSION["error"]=0;
        }
        $_SESSION["username"]=$_GET["username"];
        $_SESSION["password"]=$_GET["password"];
        $username=$_SESSION["username"];
        $password=$_SESSION["password"];
        if(!block($username)){
            if($row=fetch(query($db,"SELECT*FROM `user` WHERE `username`='$username'"))){
                if($row[2]==$password){
                    $verifycode=str_split($_SESSION["verifycode"]);
                    if($_SESSION["key"]==0){ rsort($verifycode); }else{ sort($verifycode); }
                    if($verifycode==str_split($_GET["verifycode"])){
                        query($db,"INSERT INTO `data`(`number`,`move1`,`move2`,`movetime`)VALUES('$row[1]','登入','成功','$time')");
                        session_unset();
                        $_SESSION["data"]=$row[1];
                        $_SESSION["permission"]=$row[5];
                        $_SESSION["timer"]=60;
                        ?><script>alert("登入成功");location.href="verify.php"</script><?php
                    }else{
                        $_SESSION["error"]=$_SESSION["error"]+1;
                        if($_SESSION["error"]<3){
                            ?><script>alert("圖形驗證碼有誤");location.href="index.php"</script><?php
                        }else{
                            query($db,"INSERT INTO `data`(`number`,`move1`,`move2`,`movetime`)VALUES('$row[1]','登入','失敗','$time')");
                            session_unset();
                            ?><script>alert("圖形驗證碼有誤");location.href="usererror.php"</script><?php
                        }
                    }
                }else{
                    $_SESSION["error"]=$_SESSION["error"]+1;
                    if($_SESSION["error"]<3){
                        ?><script>alert("密碼有誤");location.href="index.php"</script><?php
                    }else{
                        query($db,"INSERT INTO `data`(`number`,`move1`,`move2`,`movetime`)VALUES('$row[1]','登入','失敗','$time')");
                        session_unset();
                        ?><script>alert("密碼有誤");location.href="usererror.php"</script><?php
                    }
                }
            }else{
                $_SESSION["error"]=$_SESSION["error"]+1;
                if($_SESSION["error"]<3){
                    ?><script>alert("帳號有誤");location.href="index.php"</script><?php
                }else{
                    query($db,"INSERT INTO `data`(`number`,`move1`,`move2`,`movetime`)VALUES('未知','登入','失敗','$time')");
                    session_unset();
                    ?><script>alert("帳號有誤");location.href="usererror.php"</script><?php
                }
            }
        }else{
            $_SESSION["error"]=$_SESSION["error"]+1;
            if($_SESSION["error"]<3){
                ?><script>alert("帳號有誤");location.href="index.php"</script><?php
            }else{
                query($db,"INSERT INTO `data`(`number`,`move1`,`move2`,`movetime`)VALUES('未知','登入','失敗','$time')");
                session_unset();
                ?><script>alert("帳號有誤");location.href="usererror.php"</script><?php
            }
        }
    }else{
        ?><script>alert("未知錯誤請重新登入");location.href="index.php"</script><?php
    }
?>