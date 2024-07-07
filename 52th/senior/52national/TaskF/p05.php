<?php
    $memoryBefore=memory_get_usage();

    echo("p05\n");
    $input1=explode(" ",trim(fgets(STDIN)));//第1矩陣
    $input2=explode(" ",trim(fgets(STDIN)));//第2矩陣
    $sum=[];
    for($i=0;$i<max(count($input1),count($input2));$i=$i+1){//最大值
        if(isset($input1[$i])){ $sum[]=$input1[$i]; }else{ $sum[]=0; }
        if(isset($input2[$i])){ $sum[$i]=$sum[$i]+$input2[$i]; }else{ $sum[$i]=$sum[$i]+0; }
    }
    echo("output: \n");
    echo(implode(" ",$sum));
    echo("\n");

    $memoryAfter=memory_get_usage();
    $memoryDifference=$memoryAfter-$memoryBefore;
    echo("memory used: ".($memoryDifference/1048576)."MB");
?>
