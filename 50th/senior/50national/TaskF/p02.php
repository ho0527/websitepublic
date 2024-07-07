<?php
    $memoryBefore=memory_get_usage();

    echo ("p02\n");

    $materialcount=trim(fgets(STDIN));
    $materiallist=[];
    $datalist=[];
    $anslist=[];
    for($i=0;$i<$materialcount;$i=$i+1){
        $material=trim(fgets(STDIN));
        $materiallist[]=$material;
        $datalist[$material]=[];
    }

    $datacount=trim(fgets(STDIN));
    for($i=0;$i<$datacount;$i=$i+1){
        $data=explode(" ",trim(fgets(STDIN)));
        $datalist[$data[0]][]=[$data[1],$data[2]];
    }

    $memoryAfter=memory_get_usage();
    $memoryDifference=$memoryAfter-$memoryBefore;
    echo("memory used: ".($memoryDifference/1048576)."MB");
?>