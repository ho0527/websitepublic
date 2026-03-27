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

		<div class="bergerbar">
			<a href="./">首頁</a>
		</div>

		<main></main>

		<?php include "block/footer.php"; ?>

		<script src="initialize.js"></script>
		<script src="index.js"></script>
	</body>
</html>