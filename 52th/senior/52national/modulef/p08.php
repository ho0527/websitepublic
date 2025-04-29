<?php
    $memoryBefore=memory_get_usage();
    echo("p08\n");

    $n=trim(fgets(STDIN));
    $ans=[];
    for($i=0;$i<$n;$i=$i+1){
        $ans[]=trim(fgets(STDIN));
    }
    for($i=0;$i<count($ans);$i=$i+1){
        if((1<$ans[$i])&&($ans[$i]<(2**63-1))){
            $all=1;
            for($j=2;$j<=sqrt($ans[$i]);$j=$j+1){
                if($ans[$i]%$j==0){
                    if($j==($ans[$i]/$j)){
                        $all=$all+$j;
                    }else{
                        $all=$all+($j+$ans[$i]/$j);
                    }
                }
            }
            if($all==$ans[$i]){
                echo("Y".PHP_EOL);
            }else{
                echo("N".PHP_EOL);
            }
        }else{
            echo("N".PHP_EOL);
        }
    }

    $memoryAfter=memory_get_usage();
    $memoryDifference=$memoryAfter-$memoryBefore;
    echo("memory used: ".($memoryDifference/1048576)."MB");
?>