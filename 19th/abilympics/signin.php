<?php include "initialize.php"; ?>
<!DOCTYPE html>
<html lang="zh-Hant">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title><?= $sitetitle ?></title>
		<link rel="icon" href="media/logo/favicon-64.png">
		<link rel="stylesheet" href="index.css">
	</head>
	<body>
		<?php include "block/header.php"; ?>
		<?php include "block/nav.php"; ?>

		<div class="burgerbar">
			<a href="./">首頁</a> >
			<a href="signin.php">系統管理</a> >
			<span>登入</span>
		</div>

		<main>
			<form action="" method="POST" id="signinform" class="signinform">
				<div class="label">
					<label for="username">帳號</label>
					<input type="text" id="username" name="username" required>
				</div>
				<div class="label">
					<label for="password">密碼</label>
					<input type="password" id="password" name="password" required>
				</div>
				<div class="center">
					<input type="reset" class="button" value="重置">
					<input type="submit" class="button" value="登入">
				</div>
			</form>
		</main>

		<?php include "block/footer.php"; ?>

		<script src="initialize.js"></script>
		<script src="signin.js"></script>
	</body>
</html>
