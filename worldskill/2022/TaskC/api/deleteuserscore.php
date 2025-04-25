<?php
	include("../link.php");

	$gameversionid=$_GET["gameversionid"];
	$userid=$_GET["userid"];

	if($gameversionid&&$userid){
		$gameversionrow=query($db,"SELECT*FROM `gameversion` WHERE `id`=?",[$gameversionid]);
		$gameversionrow=query($db,"SELECT*FROM `gameversion` WHERE `gameid`=?",[$gameversionrow[0]["gameid"]]);
		for($i=0;$i<count($gameversionrow);$i=$i+1){
			query($db,"DELETE FROM `score` WHERE `gameversionid`=? AND `userid`=?",[$gameversionrow[$i]["id"],$userid]);
		}
	}

	header("location: ../gamedetail.php?slug=".$_GET["slug"]);
?>