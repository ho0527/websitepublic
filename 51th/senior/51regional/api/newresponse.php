<?php
	include("../link.php");
	$data=json_decode(file_get_contents("php://input"),true);
	if(isset($data["userid"])){
		$user=$data["userid"];
	}else{
		$user="未知";
	}
	query($db,"INSERT INTO `response`(`userid`,`questionid`,`response`)VALUES(?,?,?)",[$user,$data["questionid"],json_encode($data["response"])]);
	query($db,"INSERT INTO `log`(`username`,`move`,`movetime`,`ps`)VALUES(?,?,?,?)",[$user,"回應問卷成功",$time,"qid=".$data["questionid"]]);
	$row=query($db,"SELECT*FROM `question` WHERE `id`=?",[$data["questionid"]])[0];
	query($db,"UPDATE `question` SET `responcount`=? WHERE `id`=?",[(int)$row[4]+1,$data["questionid"]]);
	echo(json_encode([
		"success"=>true,
		"data"=>""
	]));
?>