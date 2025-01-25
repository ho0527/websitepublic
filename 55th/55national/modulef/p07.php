<?php
	$data=json_decode(readline());

	$echo="Y";

	$i=0;
	while($i<count($data)){
		$tempdata=array_slice($data,$i,2**$i);

		print_r(array_reverse($tempdata));
		print_r($tempdata);

		if($tempdata!=array_reverse($tempdata)){
			$echo="N";
			break;
		}

		$i=$i+2**$i;
	}

	echo($echo);
?>