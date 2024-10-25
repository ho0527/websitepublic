<?php
    $data=readline();

    for($i=0;$i<$data;$i=$i+1){
        $n=(int)readline();
        if($n==2){
            echo("Y");
        }else if($n%2==0){
            echo("N");
        }else{
            $check=false;
            for($j=3;$j<=sqrt($n);$j=$j+2){
                if($n%$j==0){
                    $check=true;
                    echo("N");
                    break;
                }
            }
            if(!$check)
                echo("Y");
        }
    }

?>