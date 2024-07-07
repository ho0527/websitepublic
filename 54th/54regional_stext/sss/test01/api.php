<?php
    include("link.php");

    if(isset($_GET["signout"])){
        session_unset();
        ?><script>alert("登出成功");location.href="index.php"</script><?php
    }

    if(isset($_GET["deletecomment"])){
        query("UPDATE `comment` SET `deletetime`=? WHERE `id`=?",[$time,$_GET["deletecomment"]])
        ?><script>alert("刪除成功");location.href="comment.php"</script><?php
    }

    if(isset($_GET["admindeletecomment"])){
        query("DELETE FROM `comment` WHERE `id`=?",[$_GET["admindeletecomment"]])
        ?><script>alert("刪除成功");location.href="admincomment.php"</script><?php
    }

    if(isset($_GET["adminreplycomment"])){
        query("UPDATE `comment` SET `reply`=? WHERE `id`=?",[$_GET["reply"],$_GET["adminreplycomment"]])
        ?><script>alert("回應成功");location.href="admincomment.php"</script><?php
    }

    if(isset($_GET["adminpincomment"])){
        $row=query("SELECT*FROM `comment` WHERE `id`=?",[$_GET["adminpincomment"]])[0];
        if($row["pin"]=="true"){
            $row=query("UPDATE `comment` SET `pin`='' WHERE `id`=?",[$_GET["adminpincomment"]]);
            ?><script>alert("解除置頂成功");location.href="admincomment.php"</script><?php
        }else{
            $row=query("UPDATE `comment` SET `pin`='true' WHERE `id`=?",[$_GET["adminpincomment"]]);
            ?><script>alert("置頂成功");location.href="admincomment.php"</script><?php
        }
    }

    if(isset($_GET["deleteroom"])){
        query("UPDATE `bookroom` SET `delete`='true' WHERE `id`=?",[$_GET["deleteroom"]])
        ?><script>alert("刪除成功");location.href="adminbookroom.php"</script><?php
    }

    // ajax api-->
    if(isset($_GET["getleftroom"])){
        $row=query("SELECT*FROM `bookroom` WHERE (`startday`<=?AND?<=`endday`)AND`delete`=''",[$_GET["endday"],$_GET["startday"]]);
        $leftroom=[1,2,3,4,5,6,7,8];
        for($j=0;$j<count($row);$j=$j+1){
            $room=explode(",",$row[$j]["room"]);
            for($k=0;$k<count($room);$k=$k+1){
                unset($leftroom[$room[$k]-1]);
            }
        }
        echo(json_encode(array_values($leftroom)));
    }
?>