<?php
    include("../link.php");
    $userdata=$_SESSION["data"];
    $id=$_SESSION["id"];
    $title=$_POST["title"];
    $description=$_POST["description"];
    $extendid=$_POST["extend"];
    $file="";
    $filetype="";
    if(!empty($_FILES["file"]["name"])){
        $file="upload/".$_FILES["file"]["name"];
        $filetype=mb_substr($_FILES["file"]["type"],0,5,"utf-8");
        if(file_exists("../".$file)){
            $j=1;
            while(file_exists("../".$file)){
                $file="upload/".$j."_".$_FILES["file"]["name"];
                $j=$j+1;
            }
        }
        move_uploaded_file($_FILES["file"]["tmp_name"],"../".$file);
    }
    $row=query($db,"SELECT*FROM `opinion` WHERE `project_facingid`='$id'");
    $number=str_pad(count($row)+1,3,"0",STR_PAD_LEFT);
    query($db,"INSERT INTO `opinion`(`userid`,`project_facingid`,`extendid`,`title`,`description`,`fileurl`,`filetype`,`time`,`number`)VALUES(?,?,?,?,?,?,?,?,?)",[$userdata,$id,$extendid,$title,$description,$file,$filetype,$time,$number]);
    query($db,"INSERT INTO `log`(`username`,`move`,`movetime`,`ps`)VALUES(?,?,?,?)",[$userdata,"發表意見",$time,""]);
    echo($_SESSION["id"]);
?>