<?php include "initialize.php"; ?>
<?php $page="report"; ?>

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
			<span>極光預報</span>
		</div>

		<main class="page">
			<div class="container">
				<section>
					<form action="" method="GET" class="flex">
						<div class="label">
							<label for="code">觀測地點</label>
							<select name="code" id="code">
								<option value="all" <?= ($_GET["code"]??"all")=="all"?"selected":"" ?>>全部地點</option>
								<?php
									$locationlist=query($db,"SELECT*FROM `location`");
									foreach($locationlist as $location){
										?>
										<option value="<?= $location["code"] ?>" <?= ($_GET["code"]??"")==$location["code"]?"selected":"" ?>><?= $location["name"] ?>（<?= $location["nameen"] ?>）</option>
										<?php
									}
								?>
							</select>
						</div>
						<div>
							<input type="submit" class="button" value="查詢">
						</div>
					</form>

					<?php
						$code=$_GET["code"]??"all";

						if($code=="all"){
							$reportlist=query($db,"SELECT `location`.`code`,`location`.`name`,`location`.`nameen`,`location`.`image`,`location`.`alt`,`forecast`.* FROM `forecast` INNER JOIN `location` ON `forecast`.`locationid`=`location`.`id` ORDER BY `forecast`.`probability` DESC");
						}else{
							$reportlist=query($db,"SELECT `location`.`code`,`location`.`name`,`location`.`nameen`,`location`.`image`,`location`.`alt`,`forecast`.* FROM `forecast` INNER JOIN `location` ON `forecast`.`locationid`=`location`.`id` WHERE `location`.`code`=?",[$code]);
						}

						if(count($reportlist)==0){
							//查無此地點
							?>
							<div class="message warning">查無此地點的預報資料，請重新選擇</div>
							<?php
						}else{
							?>
							<div class="cardlist">
								<?php
									foreach($reportlist as $report){
										?>
										<div class="card">
											<img src="<?= $report["image"] ?>" alt="<?= $report["alt"] ?>">
											<div class="cardbody">
												<h3><?= $report["name"] ?> <?= recommendtag($report["recommendation"]) ?></h3>
												<p class="hint"><?= $report["nameen"] ?></p>
												<?php
													if($report["kpindex"]===null){
														//該地點無資料
														?>
														<div class="message warning">此地點目前無預報資料，請稍後再試</div>
														<?php
													}else{
														?>
														<ul class="datalist">
															<li><span>Kp 指數</span><span><?= $report["kpindex"] ?></span></li>
															<li><span>雲量</span><span><?= $report["cloudcover"] ?> %</span></li>
															<li><span>極光機率</span><span><?= $report["probability"] ?> %</span></li>
															<li><span>氣溫</span><span><?= $report["temperature"] ?> °C</span></li>
															<li><span>最佳觀賞時間</span><span><?= $report["besttime"] ?></span></li>
															<li><span>推薦程度</span><span><?= recommendtag($report["recommendation"]) ?></span></li>
														</ul>
														<?php
													}
												?>
												<div class="cutbox">
													<span class="noteshort"><?= cutstr($report["note"],40) ?></span>
													<span class="notefull"><?= $report["note"] ?></span>
													<?php
														if(mb_strlen($report["note"])>40){
															?>
															<input type="button" class="button cutbutton" value="展開">
															<?php
														}
													?>
												</div>
											</div>
										</div>
										<?php
									}
								?>
							</div>
							<?php
						}
					?>
				</section>
			</div>
		</main>

		<?php include "block/footer.php"; ?>

		<script src="initialize.js"></script>
		<script src="report.js"></script>
	</body>
</html>
