<?php
	include("../link.php");

	$row=query($db,"TRUNCATE TABLE `score`",[]);

	header("location: ../game.php");
?>