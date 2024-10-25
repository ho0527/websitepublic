<?php
	$data=readline();
	$a=0;
	$b=1;

	for($i=0;$i<$data;$i=$i+1){
		$arrtemp=$a;
		$a=bcadd($b,0,0);
		$b=bcadd($arrtemp,$b,0);
	}

	echo($a);
?>