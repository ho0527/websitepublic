<?php
    $memorybefore=memory_get_usage();

    echo("p06\n");
    $data=trim(fgets(STDIN));
    $rpn=[];
    $operand=[];
    $maindata=explode(" ",$data);

    for($i=count($maindata)-1;$i>=0;$i=$i-1){
        if(in_array($maindata[$i],["+","-","*","/","^"])){
            $datacheck=-1;
            $begindatacheck=-1;

            if($maindata[$i]=="+"||$maindata[$i]=="-"){ $datacheck=2; }
            elseif($maindata[$i]=="*"||$maindata[$i]=="/"){ $datacheck=1; }
            elseif($maindata[$i]=="^"){ $datacheck=0; }

            if(!empty($operand)){
                if($operand[0]=="+"||$operand[0]=="-"){ $begindatacheck=2; }
                elseif($operand[0]=="*"||$operand[0]=="/"){ $begindatacheck=1; }
                elseif($operand[0]=="^"){ $begindatacheck=0; }
            }

            while(!empty($operand)&&in_array($operand[0],["+","-","*","/","^"])&&($datacheck>=$begindatacheck)){
                array_unshift($rpn,array_shift($operand));
            }
            array_unshift($operand,$maindata[$i]);
        }elseif($maindata[$i]==")"){
            array_unshift($operand,$maindata[$i]);
        }elseif($maindata[$i]=="("){
            while($operand[0]!=")"){
                array_unshift($rpn,array_shift($operand));
            }
            array_shift($operand);
        }else{
            array_unshift($rpn,$maindata[$i]);
        }
    }

    for($i=0;$i<count($operand);$i++){
        array_unshift($rpn,$operand[$i]);
    }

    echo(implode(" ",$rpn)."\n");

    // Calculate the result
    $data=str_replace("^","**",$data);
    $result=eval("return $data;");

    if(is_int($result)){
        echo($result.PHP_EOL);
    }else{
        echo(rtrim(sprintf("%.3f",$result),"0").PHP_EOL);
    }

    $memoryafter=memory_get_usage();
    $memorydifference=$memoryafter-$memorybefore;
    echo("memory used ".($memorydifference/1048576)."MB");
?>