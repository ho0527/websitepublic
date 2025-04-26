<?php
	include("../link.php");

	if(isset($_SESSION["signin"])){
		unset($_SESSION["signin"]);
	}
	header("location: ../index.php");
?>