<?php
    include("link.php");

    if(isset($_GET["signout"])){
        unset($_SESSION["islogin"]);
        ?><script>alert("登出成功");location.href="signin.php"</script><?php
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

    if(isset($_GET["deleteroomorder"])){
        $row=query($db,"UPDATE `roomorder` SET `delete`='true' WHERE `id`=?",[$_GET["deleteroomorder"]]);
        ?><script>alert("刪除成功");location.href="adminbookroom.php"</script><?php
    }
    
    // ajax api --->>
    if(isset($_GET["leftroom"])){
        $row=query($db,"SELECT*FROM `roomorder` WHERE (`startdate`<=?AND?<=`enddate`)AND`delete`='false'",[$_GET["enddate"],$_GET["startdate"]]);
        $dataleftroom=[1,2,3,4,5,6,7,8];

        for($i=0;$i<count($row);$i=$i+1){
            $bookroomlist=explode(",",$row[$i]["roomno"]);
            for($j=0;$j<count($bookroomlist);$j=$j+1){
                unset($dataleftroom[$bookroomlist[$j]-1]);
            }
        }

        $dataleftroom=array_values($dataleftroom);

        echo(json_encode($dataleftroom));
    }
    
?>