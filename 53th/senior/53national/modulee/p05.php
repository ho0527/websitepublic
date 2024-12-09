<?php
    $memorybefore=memory_get_usage();

    echo("p05\n");
    $left=(int)trim(fgets(STDIN));
    $n=(int)trim(fgets(STDIN));
    $data=explode(" ",trim(fgets(STDIN)));
    $maincount=0;

    rsort($data);

    for($i=0;$i<count($data);$i=$i+1){
        $count=(int)($left/$data[$i]);
        $left=$left%$data[$i];

        $maincount=$maincount+$count;
    }

    echo($maincount.PHP_EOL);

    $memoryafter=memory_get_usage();
    $memorydifference=$memoryafter-$memorybefore;
    echo("memory used ".($memorydifference/1048576)."MB");
?>