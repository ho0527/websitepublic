<?php
    include("link.php");
    $data=json_decode(file_get_contents("php://input"),true);
    $id=$data["id"];
    $row=query($db,"SELECT*FROM `wintime`")[0];
    $count=(int)$row[$id]+1;
    query($db,"UPDATE `wintime` SET `$id`='$count'");
    echo("finish");
?>