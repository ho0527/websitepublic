<?php
    include("../link.php");
    $data=json_decode(file_get_contents("php://input"),true);
    $userdata=$_SESSION["data"];
    $id=$_SESSION["id"];
    $projectname=$data[0];
    $projectdesciption=$data[1];
    $leader=$data[2];
    $member=implode("|&|",$data[3]);
    query($db,"UPDATE `project` SET `projectname`=?,`projectdesciption`=?,`leader`=?,`member`=? WHERE `id`='$id'",[$projectname,$projectdesciption,$leader,$member]);
    $facingrow=query($db,"SELECT*FROM `facing` WHERE `projectid`='$id'");
    $newfacing=array_combine($data[4],$data[5]);

    // Update or delete existing facings
    for($i=0;$i<count($facingrow);$i=$i+1){
        $name=$facingrow[$i][2];
        if(in_array($name,array_keys($newfacing))){
            $description=$newfacing[$name];
            if($facingrow[$i][3]!=$description){
                $facingid=$facingrow[$i][0];
                query($db,"UPDATE `facing` SET `description`=? WHERE `id`='$facingid'",[$description]);
            }
        }else{
            $facingid=$facingrow[$i][0];
            query($db,"DELETE FROM `facing` WHERE `id`='$facingid'");
        }
    }

    // Insert new facings
    for($i=0;$i<count($data[4]);$i=$i+1){
        $name=$data[4][$i];
        if(!in_array($name,array_column($facingrow,"name"))){
            $description=$data[5][$i];
            query($db,"INSERT INTO `facing`(`projectid`,`name`,`description`)VALUES(?,?,?)",[$id,$name,$description]);
        }
    }

    query($db,"INSERT INTO `log`(`username`,`move`,`movetime`,`ps`)VALUES(?,?,?,?)",[$userdata,"修改面向",$time,$id]);
?>