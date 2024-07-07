<?php
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

    $m=1;
    $ans=[];

    if(1<=$m&&$m<=10){
        for($i=0;$i<$m;$i=$i+1){
            $n="3";
            if(1<=$n&&$n<=100){
                $num=explode(" ","2 1 3");
                $numi=[];
                $str="";
                for($j=0;$j<$n;$j=$j+1){
                    if(1<=$num[$j]&&$num[$j]<=9){
                        $str=$str.$num[$j];
                        echo "\$num[\$j] ="; print_r($num[$j]); echo "<br>";
                        echo "\$str[\$j] ="; print_r($str); echo "<br>";
                    }else{
                        $ans[]="輸入未符合要求(num[i])";
                        break;
                    }
                }
                $result=permutation($str);
                rsort($result);
                if($result[0]==$str){
                    $ans[]=$result[count($result)-1];
                }else{
                    $key=0;
                    for($k=0;$k<count($result);$k=$k+1){
                        if($result[$k]==$str){
                            $key=$k;
                        }
                    }
                    $ans[]=$result[$key-1];
                }
            }else{
                $ans[]="輸入未符合要求(n)";
            }
        }

        for($i=0;$i<count($ans);$i=$i+1){
            echo($ans[$i]."\n");
        }
    }else{
        echo("輸入未符合要求(m)");
    }

?>