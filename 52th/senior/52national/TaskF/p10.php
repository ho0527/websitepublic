<?php
    $n=(int)trim(fgets(STDIN));
    $ans=[];
    for($i=0;$i<$n;$i=$i+1){
        $input=trim(fgets(STDIN));
        if(preg_match("/^[0-9]+((\s[0-9]+)+)?$/",$input)){
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
            if((($sum%10)==0)){
                $ans[]="Y";
            }else{
                $ans[]="N";
            }
        }else{
            $ans[]="N";
        }
    }
    for($i=0;$i<count($ans);$i=$i+1){
        echo($ans[$i].PHP_EOL);
    }
?>