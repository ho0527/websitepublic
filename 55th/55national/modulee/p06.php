<?php
	$data=readline();

	$stack=new SplStack();
	$maxlen=0;
	$stack->push(-1);

	for($i=0;$i<strlen($data);$i=$i+1){
		if($data[$i]=="("){
			$stack->push($i);
		}else{
			$stack->pop();
			if($stack->isEmpty()){
				$stack->push($i);
			}else{
				$maxlen=max($maxlen,$i-$stack->top());
			}
		}
	}

	echo($maxlen);
?>