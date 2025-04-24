<?php
	include("../link.php");

	$slug=$_GET["slug"];

	if($slug){
		$row=query($db,"SELECT*FROM `game` WHERE `slug`=?",[$slug]);
		if($row){
			query($db,"UPDATE `game` SET `deletetime`=? WHERE `slug`=?",[$time,$slug]);
		}
	}

	header("location: ../game.php");
?>