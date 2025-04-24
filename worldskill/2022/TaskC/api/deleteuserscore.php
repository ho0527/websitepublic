<?php
	include("../link.php");

	$id=$_GET["id"];
	$userid=$_GET["id"];

	if($id&&$userid){
		query($db,"DELETE FROM `score` WHERE `id`=? AND `userid`=?",[$id,$userid]);
	}

	header("location: ../game.php");
?>