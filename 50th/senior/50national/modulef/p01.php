<?php
    $memoryBefore=memory_get_usage();
    echo("p10\n");

    $n=(int)trim(fgets(STDIN));
    $ans=[];
    for($i=0;$i<$n;$i=$i+1){
        $input=trim(fgets(STDIN));
        $input=preg_replace("/[^\d]/","",$input);
        $sum=0;
        $odd=strlen($input)%2;
        for($j=0;$j<strlen($input);$j=$j+1){
            $digit=(int)$input[$j];
            if(($j+$odd)%2==0){
                $digit=$digit*2;
            }
            if($digit>9){
               $sum=$sum+$digit-9;
            } else{
               $sum=$sum+$digit;
            }
        }
        if((($sum%10)==0)&&preg_match("/[0-9]/",$input)){
            $ans[]="Y";
        }else{
            $ans[]="N";
        }
    }
    for($i=0;$i<count($ans);$i=$i+1){
        echo($ans[$i].PHP_EOL);
    }
    echo("\n");

    $memoryAfter=memory_get_usage();
    $memoryDifference=$memoryAfter-$memoryBefore;
    echo("memory used: ".($memoryDifference/1048576)."MB");
?>