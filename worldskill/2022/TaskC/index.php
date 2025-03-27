<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1.0">
        <title>Document</title>
        <link rel="stylesheet" href="/index.css">
        <link rel="stylesheet" href="index.css">
        <link rel="stylesheet" href="/chrisplugin/css/chrisplugin.css">
        <script src="/chrisplugin/js/chrisplugin.js"></script>
    </head>
    <body>
        <form method="POST" class="main center">
			<div class="inputdiv">
				<div class="label">username</div>
				<div class="input endicon underline">
					<input type="text" id="username">
					<div class="icon"><img src="/material/icon/user.svg" class="iconinputicon" draggable="false">
					</div>
				</div>
			</div>
			<div class="inputdiv">
				<div class="label">password</div>
				<div class="input endicon underline">
					<input type="text" id="password">
					<div class="icon"><img src="/material/icon/eyeclose.svg" class="iconinputicon cursor-pointer" id="passwordicon" draggable="false"></div>
				</div>
			</div>
			<div class="text error bold textcenter inputdiv" id="error"></div>
			<div class="inputdiv">
				<input type="submit" class="button outline fill margin-0px" id="submit" value="送出">
			</div>
        </form>

		<?php
			include("link.php");

			if(isset($_POST["submit"])){
				$username=$_POST["username"];
				$password=$_POST["password"];

				if($row=query($db,"SELECT*FROM `admin` WHERE `username`=? AND `password`=?",[$username,$password])){
					$_SESSION[""]=true;
					?><script>alert("signin success:(");location.href="./"</script><?php
				}else{
					?><script>alert("signin faild:(");location.href="./"</script><?php
				}
			}
		?>
    </body>
</html>