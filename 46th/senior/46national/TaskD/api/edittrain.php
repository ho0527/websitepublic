<?php
    include("../link.php");
    $data=json_decode(file_get_contents("php://input"),true); // [id,code,traintype,week,starttime,stationcount,station,price,arrivetime,stoptime]
    $id=$data[0];
    $code=$data[1];
    $traintype=$data[2];
    $week=$data[3];
    $starttime=$data[4];
    $stationcount=$data[5];
    query($db,"UPDATE `train` SET `traintypeid`=?,`code`=?,`week`=?,`starttime`=? WHERE `id`=?",[$traintype,$code,$week,$starttime,$id]);
    query($db,"DELETE FROM `stop` WHERE `trainid`=?",[$id]);
    for($i=0;$i<$stationcount;$i=$i+1){
        query($db,"INSERT INTO `stop`(`trainid`,`stationid`,`price`,`arrivetime`,`stoptime`)VALUES(?,?,?,?,?)",[$id,$data[6][$i],$data[7][$i],$data[8][$i],$data[9][$i]]);
    }
?>