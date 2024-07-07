<?php
    $memoryBefore=memory_get_usage();
    echo("p06\n");

    $n=trim(fgets(STDIN));
    $data=[];
    for($i=0;$i<$n;$i=$i+1){
        $number=trim(fgets(STDIN));
        $factor=[];
        for($j=1;$j<=$number;$j=$j+1){
            if($number%$j==0){
                $factor[]=$j;
            }
        }
        $data[]=$factor;
    }
    $k=0;
    $num1=$data[0];
    while($k<count($data)-1){
        $ans=[];
        $num2=$data[$k+1];
        for($l=0;$l<count($num1);$l=$l+1){
            for($m=0;$m<count($num2);$m=$m+1){
                if($num1[$l]==$num2[$m]){
                    $ans[]=$num1[$l];
                    break;
                }
            }
        }
        $k=$k+1;
        $num1=$ans;
    }
    echo($ans[count($ans)-1].PHP_EOL);

    $memoryAfter=memory_get_usage();
    $memoryDifference=$memoryAfter-$memoryBefore;
    echo("memory used: ".($memoryDifference/1048576)."MB");
?>