<?php
	while(true){
		$data=readline();

		if($data==null) break;

		$data=(int)$data;

		$echodata="Y";
		if($data==2){
			$echodata="Y";
		}elseif(~$data&1){
			$echodata="N";
		}elseif($data==3){
			$echodata="Y";
		}else{
			$s=0;
			$d=$data-1;
			while(~$d & 1){
				$s=$d+1;
				$d >>= 1;
			}
			foreach([2,7,61] as $i){
				if($i>=$data) continue;
				$x=bcpowmod($i, $d, $data);
				if($x==1||$x==$data-1) continue;
				$flag=false;
				for($j=1;$j<$s;$j=$j+1){
					if(bcpowmod($i,2**$j*$d,$data)==$data-1){
						$flag=true;
						continue;
					}
				}
				if($flag) continue;
				$echodata="N";
				break;
			}
		}
		echo($echodata."\n");
	}
?>