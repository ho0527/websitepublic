<?php
    include("link.php");
    $data=json_decode(file_get_contents("php://input"),true);
    $id=$data["id"];
    query($db,"UPDATE `now` SET `$id`='X'");
    echo("finish");
?>