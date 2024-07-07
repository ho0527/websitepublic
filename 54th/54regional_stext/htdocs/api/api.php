<?php
    include("../link.php");
    if(isset($_GET["logout"])){
        if(isset($_SESSION["userid"])){
            $row=query($db,"SELECT*FROM `user` WHERE `id`=?",[$_SESSION["userid"]]);
            query($db,"INSERT INTO `log`(`usernumber`,`username`,`name`,`movetime`,`action`,`success`)VALUES(?,?,?,?,?,?)",[$row[0][5],$row[0][3],$row[0][3],$time,"登出","成功"]);
        }else{
            query($db,"INSERT INTO `log`(`usernumber`,`username`,`name`,`movetime`,`action`,`success`)VALUES(?,?,?,?,?,?)",["未知","","",$time,"登出","成功"]);
        }
        session_unset();
        ?><script>alert("登出成功");location.href="../index.php"</script><?php
    }

    if(isset($_GET["deluser"])){
        $id=$_GET["id"];
        if($id>1){
            query($db,"DELETE FROM `user` WHERE `id`=?",[$id]);
            ?><script>alert("刪除成功");location.href="../admin.php"</script><?php
        }else{
            ?><script>alert("禁止刪除此帳號");location.href="../admin.php"</script><?php
        }
    }
?>