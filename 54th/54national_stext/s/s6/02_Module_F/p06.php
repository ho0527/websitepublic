<?php
    $data=readline();
    $s=[-1];
    $ans=0;

    for($i=0;$i<strlen($data);$i=$i+1){
        if($data[$i]=="("){
            $s[]=$i;
        }elseif(count($s)==1){
            $s[0]=$i;
        }else{
            array_pop($s);
            print_r($s);
            $ans=max($ans,$i-$s[count($s)-1]);
        }
    }

    echo($ans);
?>