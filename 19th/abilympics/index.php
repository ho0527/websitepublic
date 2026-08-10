<?php include "initialize.php"; ?>
<?php $page="index"; ?>

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
			<span>首頁</span>
		</div>

		<main class="page">
		</main>

		<?php include "block/footer.php"; ?>

		<script src="initialize.js"></script>
		<script src="index.js"></script>
	</body>
</html>
