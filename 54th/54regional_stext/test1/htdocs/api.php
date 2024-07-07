<?php
    include("link.php");

    if(isset($_GET["logout"])){
        session_unset();
        ?><script>alert("登出成功");location.href="signup.php"</script><?php
    }

    if(isset($_GET["deletecomment"])){
        query($db,"UPDATE `comment` SET `deletetime`=? WHERE `id`=?",[$time,$_GET["deletecomment"]])
        ?><script>alert("刪除成功");location.href="comment.php"</script><?php
    }

    if(isset($_GET["admindeletecomment"])){
        query($db,"DELETE FROM `comment` WHERE `id`=?",[$_GET["admindeletecomment"]])
        ?><script>alert("刪除成功");location.href="admincomment.php"</script><?php
    }
    
    if(isset($_GET["pincomment"])){
        $row=query($db,"SELECT*FROM `comment` WHERE `id`=?",[$_GET["pincomment"]])[0];
        if($row["pin"]==""){
            query($db,"UPDATE `comment` SET `pin`=''");
            query($db,"UPDATE `comment` SET `pin`='true' WHERE `id`=?",[$_GET["pincomment"]]);
            ?><script>alert("置頂成功");location.href="admincomment.php"</script><?php
        }else{
            query($db,"UPDATE `comment` SET `pin`=''");
            ?><script>alert("取消置頂成功");location.href="admincomment.php"</script><?php
        }
    }

    if(isset($_GET["deletefood"])){
        query($db,"DELETE FROM `food` WHERE `id`=?",[$_GET["deletefood"]])
        ?><script>alert("刪除成功");location.href="adminfood.php"</script><?php
    }
?>