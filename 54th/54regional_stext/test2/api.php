<?php
    include("link.php");

    if(isset($_GET["signout"])){
        unset($_SESSION["issignin"]);
        // session_unset();
        ?><script>alert("登出成功");location.href="index.php"</script><?php
    }

    if(isset($_GET["deletecomment"])){
        query($db,"UPDATE `comment` SET `deletetime`=? WHERE `id`=?",[$time,$_GET["deletecomment"]]);
        ?><script>alert("刪除成功");location.href="comment.php"</script><?php
    }

    if(isset($_GET["pincomment"])){
        if($row=query($db,"SELECT*FROM `comment` WHERE `id`=?",[$_GET["pincomment"]])[0]["pin"]==""){
            query($db,"UPDATE `comment` SET `pin`=''",[]);
            query($db,"UPDATE `comment` SET `pin`='true' WHERE `id`=?",[$_GET["pincomment"]]);
            ?><script>alert("訂選成功");location.href="admincomment.php"</script><?php
        }else{
            query($db,"UPDATE `comment` SET `pin`=''",[]);
            ?><script>alert("解除訂選成功");location.href="admincomment.php"</script><?php
        }
    }

    if(isset($_GET["admindeletecomment"])){
        query($db,"DELETE FROM `comment` WHERE `id`=?",[$_GET["admindeletecomment"]]);
        ?><script>alert("刪除成功");location.href="admincomment.php"</script><?php
    }

    if(isset($_GET["deleteroom"])){
        $_SESSION["key".$_GET["deleteroom"]]="true";
        ?><script>alert("刪除成功");location.href="adminbookroom.php"</script><?php
    }
?>