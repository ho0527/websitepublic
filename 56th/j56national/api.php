<?php
	include("link.php");
	$key=$_GET["key"];

	if($key=="signin"){
		$row=query("SELECT*FROM `user` WHERE `username`=? AND `password`=?",[$_POST["username"],$_POST["password"]]);
		if($row){
			session_start();
			$_SESSION["signin"]=true;
			$_SESSION["userid"]=$_POST["id"];
			$_SESSION["username"]=$_POST["username"];
			header("location: profile.php");
		}else{
			?>
			<script>
				alert("登入失敗，請檢查帳號密碼是否正確");
				location.href="signin.php";
			</script>
			<?php
		}
	}elseif($key=="signup"){
		$row=query("SELECT*FROM `user` WHERE `username`=?",[$_POST["username"]]);
		if($_POST["password"]==$_POST["password-confirm"]){
			if(!$row){
				query("INSERT INTO `user`(`username`,`email`,`password`) VALUES (?,?,?)",[$_POST["username"],$_POST["email"],$_POST["password"]]);
				?>
				<script>
					alert("註冊成功，請重新登入");
					location.href="signin.php";
				</script>
				<?php
			}else{
				?>
				<script>
					alert("帳號已存在");
					location.href="signup.php";
				</script>
				<?php
			}
		}else{
			?>
			<script>
				alert("密碼與確認密碼不一致");
				location.href="signup.php";
			</script>
			<?php
		}
	}elseif($key=="signout"){
		unset($_SESSION["signin"]);
		unset($_SESSION["userid"]);
		unset($_SESSION["username"]);
		header("location: index.php");
	}else{
		header("location: index.php");
	}
?>