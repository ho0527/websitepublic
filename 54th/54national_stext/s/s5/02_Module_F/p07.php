<?php
    $data=json_decode(readline());
    $index=0;
    $i=0;
    $ans="Y";
    while($i<count($data)){
        $temparr=array_slice($data,$i,2**$index);

        if($temparr!=array_reverse($temparr)){
            $ans="N";
            break;
        }

        $i=$i+2**$index;
        $index=$index+1;
    }

    echo($ans);
?>