<?php
	include("signin.php");
	if(isset($_POST["submit"])){
		$email=$_POST["email"];
		$password=$_POST["password"];
		if($row=query($db,"SELECT*FROM `organizers` WHERE `email`=? AND `password`=?",[$email,$password])){
			$_SESSION["signin"]=true;
			$_SESSION["id"]=$row[0]["id"];
			$_SESSION["name"]=$row[0]["name"];
			?><script>alert("Invalid email or password");location.href="../events/";</script><?php
		}else{
			?><script>alert("Invalid email or password");location.href="../";</script><?php
		}
	}
	header("location: ../");
?>