<?php
    $phone=$_POST["phone"];
    $code=$_POST["code"];
    $getgodate=$_POST["getgodate"];
    $count=$_POST["count"];
    $startstation=$_POST["startstation"];
    $endstation=$_POST["endstation"];
    $traincode=$_POST["traincode"];
    $startstop=$_POST["startstop"];
    $total=$_POST["total"];

    // 簡訊部分
    $file=fopen("../SMS/".$phone.".txt","a");
    fwrite($file,"========================================\n列車訂位成功。訂票編號: ".$code."，".$getgodate."，".$startstation."站 到 ".$endstation."站 ".$traincode."車次\n".$count."張票，".$startstop."開，共".$total."元\n");
    fclose($file);

    echo(json_encode([
        "success"=>true,
        "data"=>""
    ]));
?>