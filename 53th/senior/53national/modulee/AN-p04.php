<?php
    $memorybefore=memory_get_usage();

    echo("p04\n");
    $maindata=[];
    $data=explode(" ",trim(fgets(STDIN)));
    $n=$data[0];
    $l=$data[1];
    $v0=explode(" ",trim(fgets(STDIN)));
    if(count($v0)==$l){
        $a=(int)trim(fgets(STDIN));
        $ans=[];
        if($a==1){
            for($i=0;$i<$n;$i=$i+1){
                $vi=explode(" ",trim(fgets(STDIN)));
                $maindata[]=$vi;
                $count=0;
                for($j=0;$j<$l;$j=$j+1){
                    $count=$count+(($v0[$j]-$vi[$j])**2);
                }
                $ans[$i]=sqrt($count);
            }
        }elseif($a==2){
            for($i=0;$i<$n;$i=$i+1){
                $vi=explode(" ",trim(fgets(STDIN)));
                $maindata[]=$vi;
                $count=0;
                for($j=0;$j<$l;$j=$j+1){
                    if($v0[$j]!=$vi[$j]){ $count=$count+1; }
                }
                $ans[$i]=$count;
            }
        }
        asort($ans);
        echo(implode(" ",$maindata[array_key_first($ans)]).PHP_EOL);
    }else{ echo("ERROR".PHP_EOL); }

    $memoryafter=memory_get_usage();
    $memorydifference=$memoryafter-$memorybefore;
    echo("memory used ".($memorydifference/1048576)."MB");
?>