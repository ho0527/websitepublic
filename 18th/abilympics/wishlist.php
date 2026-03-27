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
			<a href="./">首頁</a> >
			<span>大家的願望</span>
		</div>

		<main class="align-start">
			<table>
				<tr>
					<th>姓名</th>
					<th>願望內容</th>
					<th>愛心數量</th>
					<th>功能區</th>
				</tr>
				<?php
					$wishlist=query($db,"SELECT*FROM `wish`");
					foreach($wishlist as $wish){
						?>
						<tr class="withtr" id="wish_<?= $wish["id"] ?>" data-content='<?= json_encode(query($db,"SELECT `content` FROM `wishlike` WHERE `wishid`=?",[$wish["id"]])) ?>'>
							<td class="center"><?= $wish["name"] ?></td>
							<td><?= $wish["content"] ?></td>
							<td class="likehover center" id="likecount_<?= $wish["id"] ?>" data-id="<?= $wish["id"] ?>">
								<?= count(query($db,"SELECT*FROM `wishlike` WHERE `wishid`=?",[$wish["id"]])) ?>
							</td>
							<td>
								<input type="button" class="likehover button like" data-id="<?= $wish["id"] ?>" value="愛心">
							</td>
							<td class="position-relative">
								<div id="likecontent_<?= $wish["id"] ?>" class="likecontent"></div>
							</td>
						</tr>
						<?php
					}
				?>
			</table>
		</main>

		<?php include "block/footer.php"; ?>

		<script src="initialize.js"></script>
		<script src="wishlist.js"></script>
	</body>
</html>