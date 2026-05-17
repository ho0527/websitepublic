<?php
	$db=new PDO("mysql:host=127.0.0.1;dbname=56jnational;charset=utf8mb4","root","");
	session_start();
	date_default_timezone_set("Asia/Taipei");
	$time=date("Y-m-d H:i:s");

	function query(string $sql,$data=[]){
		global $db;
		$p=$db->prepare($sql);
		$p->execute($data);
		return $p->fetchAll();
	}
?>