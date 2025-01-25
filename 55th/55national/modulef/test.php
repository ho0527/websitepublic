<?php
	$start_time=microtime(true);
// 181440000 // 190569292
	for($i=0;$i<181440000;$i++){
		print("Hello World");
	}

	$end_time=microtime(true);
	$execution_time=$end_time - $start_time;

	echo("程式執行時間： $execution_time 秒");
?>