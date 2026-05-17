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
			<form action="api.php?key=signup" method="POST" class="register-form">
				<div>
					<label for="username">帳號:</label>
					<input type="text" class="username-input" id="username" name="username" required>
				</div>
				<div>
					<label for="email">電子郵件:</label>
					<input type="email" class="email-input" id="email" name="email" required>
				</div>
				<div>
					<label for="password">密碼:</label>
					<input type="password" class="password-input" id="password" name="password" required>
				</div>
				<div>
					<label for="confirm_password">確認密碼:</label>
					<input type="password" class="password-confirm-input" id="confirm_password" name="password-confirm" required>
				</div>
				<button type="submit" class="register-submit-button">送出</button>
			</form>
		</div>
	</body>
</html>