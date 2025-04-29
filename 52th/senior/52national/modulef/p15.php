<?php
    $memoryBefore=memory_get_usage();
    echo("p15\n");

    $count=array_count_values(preg_split("/\W+/",strtolower(trim(fgets(STDIN))),-1,PREG_SPLIT_NO_EMPTY));

    uksort($count,function($a,$b)use($count){ return $count[$a]<$count[$b]||($count[$a]==$count[$b]&&$count[$a]>$count[$b]); });

    $ans=array_slice(array_keys($count),0,3);

    // 將結果輸出到 STDOUT
    for($i=0;$i<count($ans);$i=$i+1){
        echo($ans[$i]."\n");
    }
    echo("\n");

    $memoryAfter=memory_get_usage();
    $memoryDifference=$memoryAfter-$memoryBefore;
    echo("memory used: ".($memoryDifference/1048576)."MB");
?>