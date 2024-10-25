<?php
    $data=readline();
    $count=0;
    $stack=new SlpStack;
    $stack->push(-1);
    for($i=0;$i<strlen($data);$i=$i+1){
        if($data[$i]=="("){
            $stack->push("(");
        }else{
            $stack->pop();

        }
    }
    echo($count);
    echo("沒有:(");
?>