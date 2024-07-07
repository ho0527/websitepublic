<?php
    include("link.php");

    if(isset($_GET["signout"])){
        session_unset();
        ?><script>alert("登出成功");location.href="index.php"</script><?php
    }

    if(isset($_GET["deletecomment"])){
        query("UPDATE `comment` SET `deletetime`=? WHERE `id`=?",[$time,$_GET["deletecomment"]]);
        ?><script>alert("刪除成功");location.href="comment.php"</script><?php
    }

    if(isset($_GET["admindeletecomment"])){
        query("DELETE FROM `comment` WHERE `id`=?",[$_GET["admindeletecomment"]]);
        ?><script>alert("刪除成功");location.href="admincomment.php"</script><?php
    }

    if(isset($_GET["pincomment"])){
        $row=query("SELECT*FROM `comment` WHERE `id`=?",[$_GET["pincomment"]])[0];
        query("UPDATE `comment` SET `pin`=? WHERE `id`=?",[$row["pin"]==""?"pin":"",$_GET["pincomment"]]);
        ?><script>alert("修改成功");location.href="admincomment.php"</script><?php
    }

    if(isset($_GET["deletebookroom"])){
        query("UPDATE `bookroom` SET `delete`='true' WHERE `id`=?",[$_GET["deletebookroom"]]);
        ?><script>alert("刪除成功");location.href="adminbookroom.php"</script><?php
    }

    // ajax-->
    if(isset($_GET["getleftroom"])){
        $leftroom=[1,2,3,4,5,6,7,8];
        $row=query("SELECT*FROM `bookroom` WHERE (`startday`<=? AND ?<=`endday`) AND `delete`=''",[$_GET["endday"],$_GET["startday"]]);
        for($j=0;$j<count($row);$j=$j+1){
            unset($leftroom[$row[$j]["room"]-1]);
        }
        echo(json_encode(array_values($leftroom)));
    }
?>