<?php
    $data=(int)readline();
    $a=0;
    $b=0;
    $c=0;
    $d=0;
    $a=(int)($data/50);
    $data=$data%50;
    $b=(int)($data/10);
    $data=$data%10;
    $c=(int)($data/5);
    $data=$data%5;
    $d=$data;
    echo("1 $d\n5 $c\n10 $b\n50 $a\n".($a+$b+$c+$d));
?>