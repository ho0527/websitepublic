<?php
    include("link.php");
    $data=[];
    $busrow=query($db,"SELECT*FROM `bus`");
    $siterow=query($db,"SELECT*FROM `site`");
    $data[]=$busrow;
    $data[]=$siterow;
    echo(json_encode($data));
?>