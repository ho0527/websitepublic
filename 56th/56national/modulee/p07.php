<?php
	$data=json_decode(readline());

	$echo="Y";

	$idx=1;
	$i=1;
	while($idx<count($data)){
		$tempdata=array_slice($data,$idx,2**($i));

		if($tempdata!=array_reverse($tempdata)){
			$echo="N";
			break;
		}

		$idx=$idx+2**$i;
		$i=$i+1;
	}

	echo($echo);
?>