<?php
    $memorybefore=memory_get_usage();

    echo("p05\n");
    $n=(int)trim(fgets(STDIN));
    $one=0;
    $five=0;
    $ten=0;
    $fit=0;
    $left=$n;
    $fit=(int)($left/50);
    $left=$left%50;
    $ten=(int)($left/10);
    $left=$left%10;
    $five=(int)($left/5);
    $left=$left%5;
    $one=(int)$left;
    $total=$fit+$ten+$five+$one;

    echo("1 ".$one."\n"."5 ".$five."\n"."10 ".$ten."\n"."50 ".$fit."\n".$total.PHP_EOL);

    $memoryafter=memory_get_usage();
    $memorydifference=$memoryafter-$memorybefore;
    echo("memory used ".($memorydifference/1048576)."MB");
?>