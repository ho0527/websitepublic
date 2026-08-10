<?php include "initialize.php"; ?>
<?php $page="admin"; ?>

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
			<a href="admin.php">系統管理</a> >
			<span>日記管理</span>
		</div>

		<main class="page">
			<div class="container">
				<?php
					$keyword=$_GET["keyword"]??"";
					$location=$_GET["location"]??"";
					$email=$_GET["email"]??"";
					$orderby=$_GET["orderby"]??"date";
					$ordertype=$_GET["ordertype"]??"DESC";
					$pagecount=5;
					$nowpage=isset($_GET["page"])?intval($_GET["page"]):0;
				?>

				<section>
					<form action="" method="GET" class="flex">
						<div class="label">
							<label for="keyword">搜尋（暱稱／心得）</label>
							<input type="text" id="keyword" name="keyword" value="<?= $keyword ?>">
						</div>
						<div class="label">
							<label for="location">篩選地點</label>
							<select name="location" id="location">
								<option value="">全部地點</option>
								<?php
									$locationlist=query($db,"SELECT*FROM `location`");
									foreach($locationlist as $row){
										?>
										<option value="<?= $row["name"] ?>" <?= $location==$row["name"]?"selected":"" ?>><?= $row["name"] ?></option>
										<?php
									}
								?>
							</select>
						</div>
						<div class="label">
							<label for="orderby">排序欄位</label>
							<select name="orderby" id="orderby">
								<option value="date" <?= $orderby=="date"?"selected":"" ?>>日期</option>
								<option value="rating" <?= $orderby=="rating"?"selected":"" ?>>評分</option>
								<option value="blesscount" <?= $orderby=="blesscount"?"selected":"" ?>>祝福數量</option>
								<option value="id" <?= $orderby=="id"?"selected":"" ?>>編號</option>
								<option value="name" <?= $orderby=="name"?"selected":"" ?>>暱稱</option>
							</select>
						</div>
						<div class="label">
							<label for="ordertype">排序方式</label>
							<select name="ordertype" id="ordertype">
								<option value="ASC" <?= $ordertype=="ASC"?"selected":"" ?>>升冪</option>
								<option value="DESC" <?= $ordertype=="DESC"?"selected":"" ?>>降冪</option>
							</select>
						</div>
						<div>
							<input type="submit" class="button" value="送出">
							<input type="button" class="button" onclick="location.href='admin.php'" value="重設條件">
						</div>
					</form>

					<?php
						$data=[];

						if($email!=""){
							//點擊 Email 後列出該 Email 的所有投稿
							$diarylist=query($db,"SELECT*FROM `diary` WHERE `email`=?",[$email]);
						}else{
							$diarylist=query($db,"SELECT*FROM `diary` WHERE (`name` LIKE ? OR `content` LIKE ?)",["%".$keyword."%","%".$keyword."%"]);
						}

						foreach($diarylist as $diary){
							if($location!=""&&$diary["location"]!=$location){
								continue;
							}
							$data[]=[
								"id"=>$diary["id"],
								"name"=>$diary["name"],
								"email"=>$diary["email"],
								"location"=>$diary["location"],
								"date"=>$diary["date"],
								"rating"=>$diary["rating"],
								"content"=>$diary["content"],
								"photo"=>$diary["photo"],
								"blesscount"=>count(query($db,"SELECT*FROM `blessing` WHERE `diaryid`=?",[$diary["id"]]))
							];
						}

						usort($data,function($a,$b){
							$orderby=$_GET["orderby"]??"date";
							$ordertype=$_GET["ordertype"]??"DESC";
							if($a[$orderby]==$b[$orderby]){
								return 0;
							}elseif($a[$orderby]<$b[$orderby]){
								return $ordertype=="ASC"?-1:1;
							}else{
								return $ordertype=="ASC"?1:-1;
							}
						});

						$param=$_GET;
						$param["page"]=$nowpage-1;
						$prevlink="?".http_build_query($param);
						$param["page"]=$nowpage+1;
						$nextlink="?".http_build_query($param);

						$maxpage=max(1,ceil(count($data)/$pagecount));

						if(count($data)==0){
							?>
							<div class="message warning">查無符合條件的日記資料</div>
							<?php
						}
					?>

					<div class="tablewrap">
						<table class="stack">
							<tr class="headrow">
								<th>#</th>
								<th>暱稱</th>
								<th>Email</th>
								<th>觀賞地點</th>
								<th>日期</th>
								<th>評分</th>
								<th>心得</th>
								<th>照片</th>
								<th>祝福數</th>
								<th>功能</th>
							</tr>
							<?php
								for($i=$pagecount*$nowpage;$i<min($pagecount*($nowpage+1),count($data));$i=$i+1){
									$diary=$data[$i];
									?>
									<tr>
										<td data-label="#"><?= $i+1 ?></td>
										<td data-label="暱稱"><?= $diary["name"] ?></td>
										<td data-label="Email">
											<a class="emaillink" href="?email=<?= urlencode($diary["email"]) ?>"><?= $diary["email"] ?></a>
										</td>
										<td data-label="觀賞地點"><?= $diary["location"] ?></td>
										<td data-label="日期"><?= $diary["date"] ?></td>
										<td data-label="評分"><?= stars($diary["rating"]) ?></td>
										<td data-label="心得">
											<div class="cutbox">
												<span class="noteshort"><?= cutstr($diary["content"],30) ?></span>
												<span class="notefull"><?= $diary["content"] ?></span>
												<?php
													if(mb_strlen($diary["content"])>30){
														?>
														<input type="button" class="button cutbutton" value="展開">
														<?php
													}
												?>
											</div>
										</td>
										<td data-label="照片">
											<?php
												if($diary["photo"]==""){
													?>
													<span class="hint">無照片</span>
													<?php
												}else{
													?>
													<a href="<?= $diary["photo"] ?>" target="_blank"><?= basename($diary["photo"]) ?></a>
													<?php
												}
											?>
										</td>
										<td data-label="祝福數"><span class="blesscount"><?= $diary["blesscount"] ?></span></td>
										<td data-label="功能">
											<input type="button" class="button delete" data-id="<?= $diary["id"] ?>" data-name="<?= $diary["name"] ?>" value="刪除">
										</td>
									</tr>
									<?php
								}
							?>
						</table>
					</div>

					<div class="center margin_10px_0px">
						<input type="button" class="button" onclick="location.href='<?= $prevlink ?>'" value="<" <?= $nowpage<=0?"disabled":"" ?>>
						<?= $nowpage+1 ?> / <?= $maxpage ?>　（共 <?= count($data) ?> 筆）
						<input type="button" class="button" onclick="location.href='<?= $nextlink ?>'" value=">" <?= $nowpage>=($maxpage-1)?"disabled":"" ?>>
					</div>
				</section>
			</div>
		</main>

		<?php include "block/footer.php"; ?>

		<script src="initialize.js"></script>
		<script src="admin.js"></script>
	</body>
</html>
