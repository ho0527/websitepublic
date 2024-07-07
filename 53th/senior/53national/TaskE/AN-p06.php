<?php
    $memorybefore=memory_get_usage();

    echo("p06\n");
    $data=trim(fgets(STDIN));
    $rpn=[];
    $operand=[];
    $maindata=explode(" ",$data);

    for($i=0;$i<count($maindata);$i=$i+1){
        if(in_array($maindata[$i],["+","-","*","/"])){
            $datacheck=-1;
            $enddatacheck=-1;

            if($maindata[$i]=="+"||$maindata[$i]=="-"){ $datacheck=0; }
            elseif($maindata[$i]=="*"||$maindata[$i]=="/"){ $datacheck=1; }

            if(!empty($operand)){
                if($operand[count($operand)-1]=="+"||$operand[count($operand)-1]=="-"){ $enddatacheck=0; }
                elseif($operand[count($operand)-1]=="*"||$operand[count($operand)-1]=="/"){ $enddatacheck=1; }
            }

            while(!empty($operand)&&in_array($operand[count($operand)-1],["+","-","*","/"])&&($datacheck<=$enddatacheck)){
                $rpn[]=array_pop($operand);
            }
            $operand[]=$maindata[$i];
        }elseif($maindata[$i]=="("){
            $operand[]=$maindata[$i];
        }elseif($maindata[$i]==")"){
            while($operand[count($operand)-1]!="("){
                $rpn[]=array_pop($operand);
            }
            array_pop($operand);
        }else{
            $rpn[]=$maindata[$i];
        }
    }

    for($i=count($operand)-1;$i>=0;$i=$i-1){
        $rpn[]=$operand[$i];
    }

    echo(implode(" ",$rpn)."\n");
    echo(eval("return ".$data.";").PHP_EOL);

    $memoryafter=memory_get_usage();
    $memorydifference=$memoryafter-$memorybefore;
    echo("memory used ".($memorydifference/1048576)."MB");
?>