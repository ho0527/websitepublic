<?php include("link.php"); if(signed_in()){ header("location: profile.php"); exit; } ?>
<!DOCTYPE html>
<html lang="zh-Hant">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>註冊 - FunTech</title>
		<link rel="stylesheet" href="index.css">
	</head>
	<body id="register-page">
		<?php render_header(); ?>
		<main class="auth-main">
			<form action="api.php?key=signup" method="POST" class="register-form formmain">
				<h1>註冊</h1>
				<label for="username">帳號</label>
				<input type="text" class="username-input" id="username" name="username" required>
				<label for="email">電子郵件</label>
				<input type="email" class="email-input" id="email" name="email" required>
				<label for="password">密碼</label>
				<input type="password" class="password-input" id="password" name="password" required>
				<label for="confirm_password">確認密碼</label>
				<input type="password" class="password-confirm-input" id="confirm_password" name="password-confirm" required>
				<button type="submit" class="register-submit-button">送出</button>
			</form>
		</main>
	</body>
</html>
