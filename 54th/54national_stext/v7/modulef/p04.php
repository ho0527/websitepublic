<?php
    $data=readline();

    function n($value){
        function f($arr,$value2,$value3,$data){
            if($data==1){
                return $arr;
            }
            $value2;
            return f($arr,0,0,0);
        }

        return f([1],1,0,$value);
    }

    echo(implode("\n",n($data)));
?>