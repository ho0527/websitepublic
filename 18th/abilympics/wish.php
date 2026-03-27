<?php include "initialize.php"; ?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title><?= $sitetitle ?></title>
		<link rel="stylesheet" href="index.css">
	</head>
	<body>
		<?php include "block/header.php"; ?>
		<?php include "block/nav.php"; ?>

		<div class="burgerbar">
			<a href="./">首頁</a> >
			<span>許願</span>
		</div>

		<main>
			<form action="api.php?key=newwish" method="POST" class="signinform">
				<div class="label">
					<label for="name">姓名</label>
					<input type="text" id="name" name="name" required>
				</div>
				<div class="label">
					<label for="content">願望內容</label>
					<input type="text" id="content" name="content" required>
				</div>
				<div class="label">
					<label for="email">Email</label>
					<input type="email" id="email" name="email" required>
				</div>
				<div class="label">
					<label for="phone">聯絡電話</label>
					<input type="text" id="phone" name="phone" pattern="[0-9]{8,10}" required>
				</div>
				<div class="center">
					<input type="reset" class="button" value="重置">
					<input type="submit" class="button" value="送出">
				</div>
			</form>
		</main>

		<?php include "block/footer.php"; ?>

		<script src="initialize.js"></script>
		<script src="index.js"></script>
	</body>
</html>