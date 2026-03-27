<?php
	$sitetitle="Web01耶誕願望許願系統";
	date_default_timezone_set("Asia/Taipei");
	$db=new PDO("mysql:host=localhost;dbname=18thabilympics;charset=utf8","root","");
	$time=date("Y-m-d H:i:s");
	session_start();

	function query($db,$sql,$data=[]){
		$p=$db->prepare($sql);
		$p->execute($data);
		return $p->fetchAll();
	}
?>