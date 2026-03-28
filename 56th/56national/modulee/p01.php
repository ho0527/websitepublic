<?php
	$count=(int)readline();
	for($i=0;$i<$count;$i=$i+1){
		$data=(int)readline();

		$echodata="Y";
		if($data==2||$data==3){
			$echodata="Y";
		}else if($data%2==0){
			$echodata="N";
		}else{
			for($j=3;$j<=$data**0.5;$j=$j+2){
				if($data%$j==0){
					$echodata="N";
					break;
				}
			}
		}

		echo($echodata."\n");
	}
?>