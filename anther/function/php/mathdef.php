<?php
    function calculatefraction($up1,$down1,$operand,$up2,$down2){
        if($operand=="+"){
            $up=($up1*$down2)+($up2*$down2);
            $down=$down1*$down2;
            $commonfactor=commonfactor(factor($up),factor($down));
            $ans=$up/$commonfactor[count($commonfactor)-1]."/".$down/$commonfactor[count($commonfactor)-1];
        }elseif($operand=="-"){
            $up=($up1*$down2)-($up2*$down2);
            if($up<=0){
                $lowerthan0="true";
            }
            $down=$down1*$down2;
            $commonfactor=commonfactor(factor($up),factor($down));
            $ans=$up/$commonfactor[count($commonfactor)-1]."/".$down/$commonfactor[count($commonfactor)-1];
        }elseif($operand=="*"||$operand==""){
            $up=$up1*$up2;
            $down=$down1*$down2;
            $commonfactor=commonfactor(factor($up),factor($down));
            $ans=$up/$commonfactor[count($commonfactor)-1]."/".$down/$commonfactor[count($commonfactor)-1];
        }elseif($operand=="/"||$operand==""){
            $up=$up1*$down2;
            $down=$up2*$down1;
            $commonfactor=commonfactor(factor($up),factor($down));
            $ans=$up/$commonfactor[count($commonfactor)-1]."/".$down/$commonfactor[count($commonfactor)-1];
        }else{
            $ans="[WARNING]function operand type error";
        }
        return $ans;
    }

    function factor($num){
        if(is_int($num)){
            $factor=[];
            $num=abs($num);
            for($i=1;$i<=$num;$i=$i+1){
                if($num%$i==0){
                    $factor[]=$i;
                }
            }
            return $factor;
        }else{
            return "[WARNING]function variable type error";
        }
    }

    function commonfactor($num1,$num2){
        $ans=[];
        for($i=0;$i<count($num1);$i=$i+1){
            for($j=0;$j<count($num2);$j=$j+1){
                if($num1[$i]==$num2[$j]){
                    $ans[]=$num1[$i];
                    break;
                }
            }
        }
        return $ans;
    }
?>