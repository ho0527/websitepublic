<?php
    function countPermutations($n){
        $count=1;
        while($n>0){
            $count=$count*$n;
            $n=$n-1;
        }
        return $count;
    }

    function permutation($x){
        $str=strval($x);
        $result=[];
        $stack=[["",$str]];
        while(!empty($stack)){
            list($prefix,$suffix)=array_pop($stack);
            $len=strlen($suffix);
            if($len==1){
                $result[]=$prefix.$suffix;
            }else{
                for($i=0;$i<$len;$i++){
                    $first=$suffix[$i];
                    $rest=substr($suffix,0,$i).substr($suffix,$i+1);
                    $stack[]=[$prefix.$first,$rest];
                }
            }
        }
        $result2=[];
        foreach($result as $perm){
            $result2[]=$perm;
        }
        return $result2;
    }

    $str="123";
    $result=permutation($str);
    echo("Permutations of".$str."<br>");
    $mainresult=[];
    for($i=0;$i<count($result);$i=$i+1){
        echo($result[$i]);
    }
    echo("<br>");
    sort($result);
    for($i=0;$i<count($result);$i=$i+1){
        echo($result[$i]);
    }
    echo("<br>");
    echo("Total number of permutations: ".countPermutations(strlen(strval($str)))."<br>");
?>