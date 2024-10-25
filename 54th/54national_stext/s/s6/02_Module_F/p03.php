<?php
    $data=(int)readline();
    $a=1;
    $b=0;

    for($i=0;$i<$data;$i=$i+1){
        $tempa=$a;
        $a=bcadd($b,0,0);
        $b=bcadd($b,$tempa,0);
    }

    echo($b);
?>