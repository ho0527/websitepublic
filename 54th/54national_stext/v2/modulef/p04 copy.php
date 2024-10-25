<?php
	$n=readline();
	$arr=[(int)$n];

	function check($arr){
		for($i=0;$i<count($arr);$i=$i+1){
			if($arr[$i]!=1){
				return true;
			}
		}
		return false;
	}

	echo($n."\n");

	while(check($arr)){
		$temparr=[];
		$check=true;
		$break=false;

		for($i=count($arr)-1;$i>=0;$i=$i-1){
			if($arr[$i]!=1){
				$temparr=$arr;
				$temparr[$i]=$temparr[$i]-1;
				if(isset($temparr[$i+1])){
					$temparr[$i+1]=$temparr[$i+1]+1;
				}else{
					$temparr[]=1;
				}
				$temparr2=$temparr;
				rsort($arr);
				rsort($temparr);
				if($arr==$temparr){
					$check=false;
				}
				break;
			}
		}

		$arr=$temparr2;
		if($check){
			rsort($arr);
			echo(implode(" ",$arr));
			echo("\n");
		}
		if($break) break;
	}
?>