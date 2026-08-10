<?php
    include("../link.php");
    $data=json_decode(file_get_contents("php://input"),true); // [id,code,traintype,week,starttime,stationcount,station,price,arrivetime,stoptime]
    $code=$data[1];
    $traintype=$data[2];
    $week=$data[3];
    $starttime=$data[4];
    $stationcount=$data[5];
    query($db,"INSERT INTO `train`(`traintypeid`,`code`,`week`,`starttime`)VALUES(?,?,?,?)",[$traintype,$code,$week,$starttime]);
    $id=$db->lastInsertId();
    for($i=0;$i<$stationcount;$i=$i+1){
        query($db,"INSERT INTO `stop`(`trainid`,`stationid`,`price`,`arrivetime`,`stoptime`)VALUES(?,?,?,?,?)",[$id,$data[6][$i],$data[7][$i],$data[8][$i],$data[9][$i]]);
    }
?>