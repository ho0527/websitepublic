<?php
    while($data=(int)readline()){
        $echo="Y";
        if($data==2){
            $echo="Y";
        }else if($data==3){
            $echo="Y";
        }else if($data%2==0){
            $echo="N";
        }else{
            for($i=3;$i<$data**0.5;$i=$i+2){
                if($data%$i==0){
                    $echo="N";
                    break;
                }
            }
        }
        echo($echo."\n");
    }
?>