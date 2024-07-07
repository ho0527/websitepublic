<?php
    $key=explode(" ","3   2");
    $ans=[];
    for($i=0;$i<count($key);$i=$i+1){
        if($key[$i]!=""){
            $ans[]=$key[$i];
        }
    }
    print_r($ans);
?>