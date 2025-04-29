<?php
    $memoryBefore=memory_get_usage();

    echo("p13\n");
    $n=(int)(fgets(STDIN));
    $ans=[];
    for($i=0;$i<$n;$i=$i+1){
        $hashvalue=0;
        $str=trim(fgets(STDIN));
        if(strlen($str)<=65536){
            for($j=0;$j<strlen($str);$j=$j+1){
                $hashvalue=($hashvalue*31+ord($str[$j]))%(2**32);
                if($hashvalue>((2**31)-1)){
                    $hashvalue=$hashvalue-2**32;
                }elseif($hashvalue<(-2**31)){
                    $hashvalue=$hashvalue+2**32;
                }
            }
            $ans[]=$hashvalue;
        }else{
            echo("輸入未符合要求");
        }
    }
    for($i=0;$i<count($ans);$i=$i+1){
        echo($ans[$i]."\n");
    }
    echo("\n");

    $memoryAfter=memory_get_usage();
    $memoryDifference=$memoryAfter-$memoryBefore;
    echo("memory used: ".($memoryDifference/1048576)."MB");
