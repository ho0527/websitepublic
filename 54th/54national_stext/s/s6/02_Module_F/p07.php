<?php
    $data=json_decode(readline());
    $i=0;
    $index=0;
    $echo="Y";

    while($i<count($data)){
        $temparr=array_slice($data,$i,2**$index);
        if($temparr!=array_reverse($temparr)){
            $echo="N";
            break;
        }
        $index=$index+1;
        $i=2**$index-1;
    }

    echo($echo);
?>