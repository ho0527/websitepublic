<?php
    include("../link.php");
    if(isset($_POST["submit"])){
        $username=$_POST["username"];
        $password=$_POST["password"];
        $ans=$_POST["ans"];

        if(!isset($_SESSION["error"])){ $_SESSION["error"]=0; }

        if($username=="admin"&&$password=="1234"){
            ?><script>alert("登入成功")</script><?php
        }

        if($row=query($db,"SELECT*FROM `user` WHERE `username`=?",[$username])){
            if($row[0][2]==$password){
                if($ans==$_SESSION["verifycode"]){
                    session_unset();
                    $_SESSION["userid"]=$row[0][0];
                    $_SESSION["permission"]=$row[0][4];
                    $_SESSION["timer"]=30;
                    query($db,"INSERT INTO `log`(`usernumber`,`username`,`name`,`movetime`,`action`,`success`)VALUES(?,?,?,?,?,?)",[$row[0][5],$username,$row[0][3],$time,"登入","成功"]);
                    ?><script>alert("登入成功");location.href="../verify.php"</script><?php
                }else{
                    query($db,"INSERT INTO `log`(`usernumber`,`username`,`name`,`movetime`,`action`,`success`)VALUES(?,?,?,?,?,?)",[$row[0][5],$row[0][1],$row[0][3],$time,"登入","失敗"]);
                    $_SESSION["error"]=$_SESSION["error"]+1;
                    ?><script>alert("圖形驗證碼有誤")</script><?php
                }
            }else{
                query($db,"INSERT INTO `log`(`usernumber`,`username`,`name`,`movetime`,`action`,`success`)VALUES(?,?,?,?,?,?)",[$row[0][5],$row[0][1],$row[0][3],$time,"登入","失敗"]);
                $_SESSION["error"]=$_SESSION["error"]+1;
                ?><script>alert("密碼有誤")</script><?php
            }
        }else{
            query($db,"INSERT INTO `log`(`usernumber`,`username`,`name`,`movetime`,`action`,`success`)VALUES(?,?,?,?,?,?)",["未知","","",$time,"登入","失敗"]);
            $_SESSION["error"]=$_SESSION["error"]+1;
            ?><script>alert("帳號有誤")</script><?php
        }

        if($_SESSION["error"]==3){
            session_unset();
            ?><script>location.href="../error.html"</script><?php

        }else{
            ?><script>location.href="../index.php"</script><?php
        }
    }else{
        ?><script>location.href="../index.php"</script><?php
    }
?>