<!DOCTYPE html>
<html>
<head>
	<title>PHP Array Comparing</title>
</head>
<body>

	<?php

    // return a new array containing the common elements of the given two arrays.
	function compareArrays($a1, $a2){
		$data=[];
		for($i=0;$i<count($a1);$i=$i+1){
			for($j=0;$j<count($a2);$j=$j+1){
				if($a1[$i]==$a2[$j]){
					$data[]=$a1[$i];
					break;
				}
			}
		}
		return implode(" ",$data);
		//put your code here
	}

	echo(compareArrays(['red', 'green', 'yellow'], ['red', 'green', 'black'])."<br>");
	echo(compareArrays(['a', 'b', 'c', 'd', 'e'], ['a', 'b', 'c', 'd', 'e'])."<br>");
	echo(compareArrays(['a'], ['a', 'b'])."<br>");
	echo(compareArrays(['a'], ['a', 'c'])."<br>");
	echo(compareArrays(['a', 'ac', 'eb'], ['a', 'ca', 'be'])."<br>");
	echo(compareArrays(['a', 'b', 'c'], ['a', 'b', 'c'])."<br>");

	?>

</body>
</html>
