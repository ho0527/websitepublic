<?php
	$data=json_decode(readline());

	$echo="Y";

	$index=1;
	$i=1;
	while($index<count($data)){
		$tempdata=array_slice($data,$index,2**$i);

		if($tempdata!=array_reverse($tempdata)){
			$echo="N";
			break;
		}

		$index=$index+2**$i;
		$i=$i+1;
	}

	echo($echo);
?>