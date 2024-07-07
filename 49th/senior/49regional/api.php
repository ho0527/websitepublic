<?php
    include("link.php");
    if(isset($_GET["login"])){
        if($_GET["login"]){
            query($db,"INSERT INTO `data`(`number`,`move1`,`move2`,`movetime`)VALUES(?,'登入','成功','$time')",[$_GET["userid"]]);
            session_unset();
            $_SESSION["data"]=$_GET["userid"];
            $_SESSION["permission"]=$row[5];
        }else{
            query($db,"INSERT INTO `data`(`number`,`move1`,`move2`,`movetime`)VALUES(?,'登入','失敗','$time')",[$_GET["userid"]]);
            session_unset();
        }
    }

    if(isset($_GET["product"])){
        $data=query($db,"SELECT*FROM `product`");
        if(isset($_GET["id"])){
            $data=query($db,"SELECT*FROM `product` WHERE `id`=?",[$_GET["id"]]);
        }
        echo(json_encode([
            "success"=>true,
            "data"=>$data
        ]));
    }

    if(isset($_GET["game"])){
        if(isset($_GET["search"])){
            $start=$_GET["start"];
            $end=$_GET["end"];
            $keyword=$_GET["keyword"];
            $row=query($db,"SELECT*FROM `game` WHERE `visibility`='true' AND (`name` LIKE ? OR `description` LIKE ? OR `link` LIKE ?) AND (?<=`date` AND `date`<=?) ORDER BY `date` ASC",["%$keyword%","%$keyword%","%$keyword%",$start,$end]);
            echo(json_encode([
                "success"=>true,
                "data"=>$row
            ]));
        }else{
            $row=query($db,"SELECT*FROM `game` WHERE `visibility`='true' AND `pin`='' ORDER BY `date` ASC");
            $pin=query($db,"SELECT*FROM `game` WHERE `pin`='true'");
            echo(json_encode([
                "success"=>true,
                "data"=>$row,
                "pin"=>$pin
            ]));
        }
    }

    if(isset($_GET["logout"])){
        $data=$_SESSION["data"];
        $row=query($db,"SELECT*FROM `user` WHERE `number`='$data'");
        if($row){
            $row=$row[0];
            query($db,"INSERT INTO `data`(`number`,`move1`,`move2`,`movetime`)VALUES('$row[1]','登出','成功','$time')");
        }else{
            query($db,"INSERT INTO `data`(`number`,`move1`,`move2`,`movetime`)VALUES('未知','登出','成功','$time')");
        }
        session_unset();
        ?><script>alert("登出成功!");
        localStorage.removeItem("49regionalid")
        localStorage.removeItem("49regionalpermission")
        localStorage.removeItem("49regionaltimer")
        location.href="index.php"</script><?php
    }

    if(isset($_GET["pin"])){
        $id=$_GET["id"];
        query($db,"UPDATE `game` SET `pin`=''");
        query($db,"UPDATE `game` SET `pin`='true' WHERE `id`='$id'");
        echo(json_encode([
            "success"=>true,
            "data"=>""
        ]));
    }

    // if($_GET["game"]){
    //     $pagesize=4;
    //     $page=$_GET["page"];
    //     $orderby="id";
    //     $ordertype="desc";
    //     if(isset($_GET["search"])){
    //         if($_GET["search"]=="date"){
    //             $start=$_GET["start"];
    //             $end=$_GET["end"];
    //             $row=query($db,"SELECT*FROM `game` WHERE ?<=`date` AND `date`<=? ORDER BY ? ? LIMIT ? OFFSET ((?-1)*?)",[$start,$end,$orderby,$ordertype,$pagesize,$page,$pagesize]);
    //             echo(json_encode([
    //                 "success"=>true,
    //                 "data"=>$row
    //             ]));
    //         }else{
    //             $keyword=$_GET["keyword"];
    //             $row=query($db,"SELECT*FROM `game` WHERE `title` LIKE ? OR `description` LIKE ? OR `link` LIKE ? ORDER BY ? ? LIMIT ? OFFSET ((?-1)*?)",["%$keyword%","%$keyword%","%$keyword%",$orderby,$ordertype,$pagesize,$page,$pagesize]);
    //             echo(json_encode([
    //                 "success"=>true,
    //                 "data"=>$row
    //             ]));
    //         }
    //     }else{
    //         $row=query($db,"SELECT*FROM `game` ORDER BY ? ? LIMIT ? OFFSET ((?-1)*?)",[$orderby,$ordertype,$pagesize,$page,$pagesize]);
    //         echo(json_encode([
    //             "success"=>true,
    //             "data"=>$row
    //         ]));
    //     }
    // }
?>