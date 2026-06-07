<?php include("link.php"); if(signed_in()){ header("location: profile.php"); exit; } ?>
<!DOCTYPE html>
<html lang="zh-Hant">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>登入 - FunTech</title>
		<link rel="stylesheet" href="index.css">
	</head>
	<body id="login-page">
		<?php render_header(); ?>
		<main class="auth-main">
			<form action="api.php?key=signin" method="POST" class="login-form formmain">
				<h1>登入</h1>
				<label for="login-username">帳號</label>
				<input type="text" class="username-input" id="login-username" name="username" required>
				<label for="login-password">密碼</label>
				<input type="password" class="password-input" id="login-password" name="password" required>
				<button type="submit" class="login-submit-button">登入</button>
			</form>
		</main>
	</body>
</html>
