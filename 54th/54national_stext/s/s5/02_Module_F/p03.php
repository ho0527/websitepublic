<?php
    $data=readline();
    $a=0;
    $b=1;

    for($i=0;$i<$data;$i=$i+1){
        $tempa=$a;
        $a=bcadd($b,0,0);
        $b=bcadd($b,$tempa,0);
    }

    echo($a)
?>