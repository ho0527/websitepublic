<?php
    include("link.php");
    $data=json_decode(file_get_contents("php://input"),true);
    $data1=$data[1];
    $data2=$data[2];
    $data3=$data[3];
    $data4=$data[4];
    $data5=$data[5];
    $data6=$data[6];
    $data7=$data[7];
    $data8=$data[8];
    $data9=$data[9];
    query($db,"INSERT INTO `log`(`1`,`2`,`3`,`4`,`5`,`6`,`7`,`8`,`9`)VALUES('$data1','$data2','$data3','$data4','$data5','$data6','$data7','$data8','$data9')");
    echo "\$data ="; print_r($data); echo "<br>";
    // echo("finish");
?>