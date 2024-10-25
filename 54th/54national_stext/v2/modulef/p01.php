<?php
	while($data=readline()){
		$data=(int)$data;

		$echodata="Y";
		if($data==2||$data==3){
			$echodata="Y";
		}else if($data%2==0){
			$echodata="N";
		}else{
			for($i=3;$i<=$data**0.5;$i=$i+2){
				if($data%$i==0){
					$echodata="N";
					break;
				}
			}
		}

		echo($echodata."\n");
	}
?>