<?php
    $memorybefore=memory_get_usage();

    echo("p02\n");

    $n=(int)(trim(fgets(STDIN)));
    $ans=0;

    $data=[0,1,2,4];

    for($i=4;$i<=$n;$i=$i+1){
        $data[$i]=$data[$i-1]+$data[$i-2]+$data[$i-3];
    }
    echo($data[$n].PHP_EOL);

    $memoryafter=memory_get_usage();
    $memorydifference=$memoryafter-$memorybefore;
    echo("memory used ".($memorydifference/1048576)."MB");
?>