<?php
    $memorybefore=memory_get_usage();

    echo("p04\n");

    $a=str_split(trim(fgets(STDIN)));
    $b=str_split(trim(fgets(STDIN)));
    $ans=(int)(count($b)-count($a));

    for($i=0;$i<min(count($b),count($a));$i=$i+1){
        if($a[$i]!=$b[$i]){
            $ans=$ans+1;
        }
    }

    echo($ans.PHP_EOL);

    $memoryafter=memory_get_usage();
    $memorydifference=$memoryafter-$memorybefore;
    echo("memory used ".($memorydifference/1048576)."MB");
?>