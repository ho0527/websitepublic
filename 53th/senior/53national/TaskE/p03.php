<?php
    $memorybefore=memory_get_usage();

    echo("p03\n");
    $n=trim(fgets(STDIN));
    $ans=[];
    for($i=0;$i<$n;$i=$i+1){
        $data=trim(fgets(STDIN));
        $ans2=[];
        $mainans="";
        for($j=2;$j<=$data;$j=$j+1){
            while($data%$j==0){
                if(isset($ans2[$j])){
                    $ans2[$j]=$ans2[$j]+1;
                }else{
                    $ans2[$j]=1;
                }
                $data=(int)$data/$j;
            }
        }
        $count=count($ans2);
        for($k=0;$k<$count;$k=$k+1){
            $number=array_key_first($ans2);
            $numberdata=$ans2[$number];
            if($numberdata>1){
                $mainans=$mainans.$number."^".$numberdata;
            }else{
                $mainans=$mainans.$number;
            }
            if($k<$count-1){
                $mainans=$mainans."*";
            }
            unset($ans2[$number]);
        }
        $ans[]=$mainans;
    }

    for($i=0;$i<count($ans);$i=$i+1){
        echo($ans[$i]."\n");
    }

    $memoryafter=memory_get_usage();
    $memorydifference=$memoryafter-$memorybefore;
    echo("memory used ".($memorydifference/1048576)."MB");
?>