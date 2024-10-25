<?php
    $data=(int)readline();

    for($i=0;$i<$data;$i=$i+1){
        $n=readline();
        if($n==2){
            echo("Y");
        }else if($n%2==0){
            echo("N");
        }else{
            $check="Y";
            for($j=3;$j<$n**0.5;$j=$j+2){
                if($n%$j==0){
                    $check="N";
                    break;
                }
            }
            echo($check);
        }
    }
?>