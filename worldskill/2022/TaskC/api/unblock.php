<?php
	$id=$_GET["id"];

	if($id){
		$row=query($db,"SELECT*FROM `user` WHERE `id`=?",[$id]);
		if($row){
			$update=query($db,"UPDATE `user` SET `blocktime`=null AND `blockreason`='' WHERE `id`=?",[$id]);
		}
	}

	header("location: ../user.php");
?>