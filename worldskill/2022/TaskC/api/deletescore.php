<?php
	include("../link.php");

	$id=$_GET["id"];

	if($id){
		$row=query($db,"SELECT*FROM `score` WHERE `id`=?",[$id]);
		if($row){
			query($db,"DELETE FROM `score` WHERE `id`=?",[$id]);
		}
	}

	header("location: ../game.php");
?>