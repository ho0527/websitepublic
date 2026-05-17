<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Document</title>
		<link rel="stylesheet" href="index.css">
	</head>
	<body id="article">
		<header class="site-header">
			<div class="brand">
				<a href="" class="brand-link">
					<img src="" alt="" class="brand">
				</a>
			</div>
			<nav class="main-nav">
				<a href="" class="home-link">首頁</a>
				<a href="" class="games-link">遊戲</a>
				<a href="" class="friends-link">好友</a>
			</nav>
			<div class="user-area">
				<?php
					include("link.php");

					if(isset($_SESSION["signin"])){
						header("location: profile.php");
					}else{
						?>
						<a href="signin.php" class="login-link">登入</a>
						<a href="signup.php" class="register-link">註冊</a>
						<?php
					}
				?>
			</div>
		</header>
		<div class="formmain">
			<form action="api.php?key=signin" method="POST" class="login-form">
				<label for="username">帳號:</label>
				<input type="text" class="username-input" name="username" required>
				<label for="password">密碼:</label>
				<input type="password" class="password-input" name="password" required>
				<button type="submit" class="login-submit-button">登入</button>
			</form>
		</div>
	</body>
</html>