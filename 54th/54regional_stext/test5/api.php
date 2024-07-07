<?php
    include("link.php");

    if(isset($_GET["signout"])){
        session_unset();
        alert("登出成功","index.php");
    }

    if(isset($_GET["deletecomment"])){
        query("UPDATE `comment` SET `deletetime`=? WHERE `id`=?",[$time,$_GET["deletecomment"]]);
        alert("刪除成功","comment.php");
    }

    if(isset($_GET["pincomment"])){
        $row=query("SELECT*FROM `comment` WHERE `id`=?",[$_GET["pincomment"]]);
        if($row[0]["pin"]==""){
            query("UPDATE `comment` SET `pin`='pin' WHERE `id`=?",[$_GET["pincomment"]]);
            alert("置頂成功","admincomment.php");
        }else{
            query("UPDATE `comment` SET `pin`='' WHERE `id`=?",[$_GET["pincomment"]]);
            alert("解置頂成功","admincomment.php");
        }
    }

    if(isset($_GET["admindeletecomment"])){
        query("DELETE FROM `comment` WHERE `id`=?",[$_GET["admindeletecomment"]]);
        alert("刪除成功","admincomment.php");
    }

    if(isset($_GET["deletebookroom"])){
        query("DELETE FROM `bookroom` WHERE `id`=?",[$_GET["deletebookroom"]]);
        alert("刪除成功","adminbookroom.php");
    }

    // ajax api--->
    if(isset($_GET["getleftroom"])){
        $row=query("SELECT*FROM `bookroom` WHERE `startday`<=? AND ?<=`endday`",[$_GET["endday"],$_GET["startday"]]);
        $leftroom=[1,2,3,4,5,6,7,8];
        for($j=0;$j<count($row);$j=$j+1){
            unset($leftroom[$row[$j]["room"]-1]);
        }
        $leftroom=array_values($leftroom);
        echo(json_encode($leftroom));
    }
?>