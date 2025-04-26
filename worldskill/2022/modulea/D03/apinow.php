<?php
    include("link.php");
    $data=query($db,"SELECT*FROM `now`");
    echo(json_encode($data));
?>