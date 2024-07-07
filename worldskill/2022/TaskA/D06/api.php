<?php
	include("link.php");
	$row=query($db,"SELECT*FROM `log`");
	echo(json_encode($row));
?>