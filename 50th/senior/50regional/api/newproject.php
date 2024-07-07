<?php
    include("../link.php");
    $data=json_decode(file_get_contents("php://input"),true);
    $userdata=$_SESSION["data"];
    $projectname=$data[0];
    $projectdesciption=$data[1];
    $leader=$data[2];
    $member=implode("|&|",$data[3]);
    query($db,"INSERT INTO `project`(`projectname`,`projectdesciption`,`leader`,`member`,`canpostopinion`,`canplanscore`)VALUES(?,?,?,?,'true','false')",[$projectname,$projectdesciption,$leader,$member]);
    $projectid=$db->lastInsertId();
    for($i=0;$i<count($data[4]);$i=$i+1){
        query($db,"INSERT INTO `facing`(`projectid`,`name`,`description`)VALUES(?,?,?)",[$projectid,$data[4][$i],$data[5][$i]]);
    }
    query($db,"INSERT INTO `log`(`username`,`move`,`movetime`,`ps`)VALUES(?,?,?,?)",[$userdata,"新增面向",$time,""]);
?>