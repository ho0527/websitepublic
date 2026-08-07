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
			<div class="hero">
				<img src="media/images/hero-aurora.jpg" alt="芬蘭夜空中的極光與雪地山稜">
				<div class="herotext">
					<h2>追尋北境天空下的極光旅程</h2>
					<p>查詢今晚的極光預報，記錄屬於你的那一場光</p>
				</div>
			</div>

			<div class="container">
				<section>
					<h2>今晚推薦的觀測地點</h2>
					<p class="hint">以下為推薦程度「高」的地點，資料更新時間 2026-01-15 18:00。</p>
					<div class="cardlist">
						<?php
							$goodlist=query($db,"SELECT `location`.`name`,`location`.`nameen`,`location`.`image`,`location`.`alt`,`location`.`intro`,`forecast`.`probability`,`forecast`.`besttime`,`forecast`.`recommendation` FROM `forecast` INNER JOIN `location` ON `forecast`.`locationid`=`location`.`id` WHERE `forecast`.`recommendation`=? ORDER BY `forecast`.`probability` DESC",["高"]);
							foreach($goodlist as $good){
								?>
								<div class="card">
									<img src="<?= $good["image"] ?>" alt="<?= $good["alt"] ?>">
									<div class="cardbody">
										<h3><?= $good["name"] ?> <?= recommendtag($good["recommendation"]) ?></h3>
										<p class="hint"><?= $good["nameen"] ?></p>
										<p><?= cutstr($good["intro"],40) ?></p>
										<ul class="datalist">
											<li><span>極光機率</span><span><?= $good["probability"] ?> %</span></li>
											<li><span>最佳觀賞時間</span><span><?= $good["besttime"] ?></span></li>
										</ul>
										<a href="report.php?code=all">查看完整預報</a>
									</div>
								</div>
								<?php
							}
						?>
					</div>
				</section>

				<section>
					<h2>最新旅人日記</h2>
					<p class="hint">共 <?= count(query($db,"SELECT `id` FROM `diary`")) ?> 篇投稿，以下顯示最新 3 篇。</p>
					<?php
						$diarylist=query($db,"SELECT*FROM `diary` ORDER BY `date` DESC,`id` DESC LIMIT 3");
						foreach($diarylist as $diary){
							?>
							<div class="diarycard">
								<img src="<?= photo($diary["photo"]) ?>" alt="<?= $diary["location"] ?>的極光照片">
								<div class="diarybody">
									<h3><?= $diary["name"] ?> <?= stars($diary["rating"]) ?></h3>
									<p class="hint"><?= $diary["location"] ?>　<?= $diary["date"] ?>　<?= maskemail($diary["email"]) ?></p>
									<p><?= cutstr($diary["content"],60) ?></p>
									<p>極光祝福 <span class="blesscount"><?= count(query($db,"SELECT*FROM `blessing` WHERE `diaryid`=?",[$diary["id"]])) ?></span></p>
								</div>
							</div>
							<?php
						}
					?>
					<div class="center">
						<a href="diary.php" class="button">看全部日記</a>
						<a href="diary.php#postform" class="button">我要投稿</a>
					</div>
				</section>
			</div>
		</main>

		<?php include "block/footer.php"; ?>

		<script src="initialize.js"></script>
		<script src="index.js"></script>
	</body>
</html>
