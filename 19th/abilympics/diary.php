<?php include "initialize.php"; ?>
<?php $page="diary"; ?>

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
			<a href="diary.php">旅人日記</a> >
			<span>投稿</span>
		</div>

		<main class="page">
			<div class="container">
				<?php
					if(($_GET["success"]??"")=="1"){
						?>
						<div class="message success">投稿成功！感謝你分享這次的極光旅程</div>
						<?php
					}
				?>

				<section id="postform">
					<form action="api.php?key=newdiary" method="POST" id="diaryform" class="signinform">
						<div class="label">
							<label for="name">旅人暱稱</label>
							<input type="text" id="name" name="name" maxlength="50">
							<span class="error" id="error_name"></span>
						</div>
						<div class="label">
							<label for="email">Email</label>
							<input type="text" id="email" name="email" maxlength="120">
							<span class="error" id="error_email"></span>
						</div>
						<div class="label">
							<label for="location">觀賞地點</label>
							<select name="location" id="location">
								<option value="">請選擇地點</option>
								<?php
									$locationlist=query($db,"SELECT*FROM `location`");
									foreach($locationlist as $location){
										?>
										<option value="<?= $location["name"] ?>"><?= $location["name"] ?></option>
										<?php
									}
								?>
							</select>
							<span class="error" id="error_location"></span>
						</div>
						<div class="label">
							<label for="date">觀賞日期</label>
							<input type="date" id="date" name="date">
							<span class="error" id="error_date"></span>
						</div>
						<div class="label">
							<label for="rating">極光評分（1-5）</label>
							<select name="rating" id="rating">
								<option value="">請選擇評分</option>
								<option value="1">1 星</option>
								<option value="2">2 星</option>
								<option value="3">3 星</option>
								<option value="4">4 星</option>
								<option value="5">5 星</option>
							</select>
							<span class="error" id="error_rating"></span>
						</div>
						<div class="label">
							<label for="content">觀賞心得（至少 10 字）</label>
							<textarea id="content" name="content" rows="5"></textarea>
							<span class="error" id="error_content"></span>
						</div>
						<div class="label">
							<label for="photo">照片網址或檔名</label>
							<input type="text" id="photo" name="photo" maxlength="255" value="media/images/aurora-levi.jpg">
							<span class="error" id="error_photo"></span>
						</div>
						<div class="center">
							<input type="reset" class="button" value="重置">
							<input type="submit" class="button" value="送出">
						</div>
					</form>
				</section>

				<section>
					<?php
						$diarylist=query($db,"SELECT*FROM `diary` ORDER BY `date` DESC,`id` DESC");

						if(count($diarylist)==0){
							?>
							<div class="message warning">目前尚無任何投稿，成為第一位分享的旅人吧</div>
							<?php
						}

						foreach($diarylist as $diary){
							?>
							<div class="diarycard">
								<img src="<?= photo($diary["photo"]) ?>" alt="<?= $diary["location"] ?>的極光照片">
								<div class="diarybody">
									<h3><?= $diary["name"] ?> <?= stars($diary["rating"]) ?></h3>
									<p class="hint">
										<?= $diary["location"] ?> <?= $diary["date"] ?> <?= maskemail($diary["email"]) ?>
									</p>
									<div class="cutbox">
										<span class="noteshort"><?= cutstr($diary["content"],60) ?></span>
										<span class="notefull"><?= $diary["content"] ?></span>
										<?php
											if(mb_strlen($diary["content"])>60){
												?>
												<input type="button" class="button cutbutton" value="閱讀更多">
												<?php
											}
										?>
									</div>
									<div class="margin_10px_0px">
										極光祝福 <span class="blesscount" id="blesscount_<?= $diary["id"] ?>"><?= count(query($db,"SELECT*FROM `blessing` WHERE `diaryid`=?",[$diary["id"]])) ?></span>
										<input type="button" class="button bless" data-id="<?= $diary["id"] ?>" value="♥ 極光祝福">
									</div>
								</div>
							</div>
							<?php
						}
					?>
				</section>
			</div>
		</main>

		<?php include "block/footer.php"; ?>

		<script src="initialize.js"></script>
		<script src="diary.js"></script>
	</body>
</html>
