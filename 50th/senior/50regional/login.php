<?php
    include("link.php");
    if(isset($_POST["submit"])){
        $username=$_POST["username"];
        $password=$_POST["password"];
        if($row=query($db,"SELECT*FROM `user` WHERE `username`='$username'")[0]){
            if($row[2]==$password){
                query($db,"INSERT INTO `log`(`username`,`move`,`movetime`,`ps`)VALUES('$row[0]','登入系統','$time','')");
                session_unset();
                $_SESSION["data"]=$row[0];
                ?><script>alert("登入成功");location.href="main.php"</script><?php
            }else{
                ?><script>alert("密碼有誤");location.href="index.php"</script><?php
            }
        }else{
            ?><script>alert("帳號有誤");location.href="index.php"</script><?php
        }
    }else{
        ?><script>alert("未知錯誤請重新登入");location.href="index.php"</script><?php
    }
?>