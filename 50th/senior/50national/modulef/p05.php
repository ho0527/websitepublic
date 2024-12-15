<?php
    $memoryBefore=memory_get_usage();

    echo("p05\n");

    $n=(int)trim(fgets(STDIN));
    $keylist=[];

    for($i=0;$i<$n;$i=$i+1){
        $input=explode(" ",trim(fgets(STDIN)));
        $column=(int)$input[0];
        $row=(int)$input[1];
        $keylist[]=$column;
        if($i==$n-1){ $keylist[]=$row; }
    }

    $n=count($keylist)-1;
    $data=[];
    for($i=0;$i<$n;$i=$i+1){
        $data[$i]=[];
        for($j=0;$j<$n;$j++){
            $data[$i][$j]=0;
        }
    }

    for($i=2;$i<=$n;$i=$i+1){
        for($j=0;$j<$n-$i+1;$j=$j+1){
            $t=$j+$i-1;
            $data[$j][$t]=PHP_INT_MAX;
            for($k=$j;$k<$t;$k=$k+1){
                $cost=$data[$j][$k]+$data[$k+1][$t]+$keylist[$j]*$keylist[$k+1]*$keylist[$t+1];
                $data[$j][$t]=min($data[$j][$t],$cost);
            }
        }
    }

    print_r($data);
    echo("\n");
    echo($data[0][$n-1].PHP_EOL);

    $memoryAfter=memory_get_usage();
    $memoryDifference=$memoryAfter-$memoryBefore;
    echo("memory used: ".($memoryDifference/1048576)."MB");
?>