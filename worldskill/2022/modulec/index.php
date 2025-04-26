<!DOCTYPE html>
<html class="dark">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1.0">
        <title>Document</title>
        <link rel="stylesheet" href="/index.css">
        <link rel="stylesheet" href="index.css">
		<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        <link rel="stylesheet" href="/chrisplugin/css/chrisplugin.css">
        <script src="/chrisplugin/js/chrisplugin.js"></script>
    </head>
    <body>
		<?php include("link.php");if(isset($_SESSION["signin"])){ header("location: admin.php"); } ?>

        <form method="POST" class="main center">
			<div class="inputdiv">
				<div class="label">username</div>
				<div class="input endicon underline">
					<input type="text" name="username">
					<div class="icon"><img src="/material/icon/user.svg" class="iconinputicon" draggable="false">
					</div>
				</div>
			</div>
			<div class="inputdiv">
				<div class="label">password</div>
				<div class="input endicon underline">
					<input type="password" name="password">
					<div class="icon"><img src="/material/icon/eyeclose.svg" class="iconinputicon cursor-pointer" id="passwordicon" draggable="false"></div>
				</div>
			</div>
			<div class="text error bold textcenter inputdiv" id="error"></div>
			<div class="inputdiv">
				<input type="submit" class="button outline fill margin-0px" name="submit" value="送出">
			</div>
        </form>

		<?php
			if(isset($_POST["submit"])){
				$username=$_POST["username"];
				$password=$_POST["password"];

				if($row=query($db,"SELECT*FROM `admin` WHERE `username`=? AND `password`=?",[$username,$password])){
					query($db,"UPDATE `admin` SET `lastlogintime`=? WHERE `username`=?",[$time,$username]);
					$_SESSION["signin"]=true;
					?><script>alert("signin success:)");location.href="./admin.php"</script><?php
				}else{
					?><script>alert("signin faild:(");location.href="./"</script><?php
				}
			}
		?>
    </body>
</html>