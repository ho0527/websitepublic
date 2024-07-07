<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>

	<?php

	function removeDuplicates($arr){
		print_r(array_unique($arr));
		echo("<br>");
		//put your code here
	}

	removeDuplicates(['a', 'a', 'b', 'c', 'd']);
	removeDuplicates(['another', 'array', 'array', 'with', 'strings']);
	removeDuplicates(['b']);
	removeDuplicates(['a', 'b']);
	removeDuplicates(['a', 'aa', 'b']);
	removeDuplicates(['a', 'z', 'z']);

	?>

</body>
</html>