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
			<span>系統管理</span>
		</div>

		<main class="align-start">
			<?php
				$orderby=isset($_GET["orderby"])?$_GET["orderby"]:"id";
				$ordertype=isset($_GET["ordertype"])?$_GET["ordertype"]:"ASC";
			?>
			<div>
				<div>
					<form class="flex">
						<div class="label">
							<label for="keyword">搜尋</label>
							<input type="text" id="keyword" name="keyword" value="<?= $_GET["keyword"]??"" ?>">
						</div>
						<div class="label">
							<label for="orderby">排序</label>
							<select name="orderby" id="orderby">
								<option value="id" <?= $orderby=="id"?"selected":"" ?>>id</option>
								<option value="name" <?= $orderby=="name"?"selected":"" ?>>姓名</option>
								<option value="content" <?= $orderby=="content"?"selected":"" ?>>願望內容</option>
								<option value="email" <?= $orderby=="email"?"selected":"" ?>>email</option>
								<option value="phone" <?= $orderby=="phone"?"selected":"" ?>>電話</option>
								<option value="likecount" <?= $orderby=="likecount"?"selected":"" ?>>愛心數量</option>
							</select>
						</div>
						<div class="label">
							<label for="ordertype">排序</label>
							<select name="ordertype" id="ordertype">
								<option value="ASC" <?= $ordertype=="ASC"?"selected":"" ?>>升冪</option>
								<option value="DESC" <?= $ordertype=="DESC"?"selected":"" ?>>降冪</option>
							</select>
						</div>
						<div>
							<input type="submit" class="button" value="送出">
						</div>
					</form>
				</div>
				<table>
					<tr>
						<th>#</th>
						<th>姓名</th>
						<th>願望內容</th>
						<th>email</th>
						<th>電話</th>
						<th>愛心數量</th>
					</tr>
					<?php
						$pagecount=1;
						$nowpage=isset($_GET["page"])?intval($_GET["page"]):0;
						$keyword=isset($_GET["keyword"])?$_GET["keyword"]:"";
						$data=[];

						$wishlist=query($db,"SELECT*FROM `wish` WHERE `name` LIKE ? OR `content` LIKE ? OR `email` LIKE ? OR `phone` LIKE ?",["%".$keyword."%","%".$keyword."%","%".$keyword."%","%".$keyword."%"]);
						for($i=0;$i<count($wishlist);$i=$i+1){
							$wish=$wishlist[$i];
							$data[]=[
								"id"=>$wish["id"],
								"name"=>$wish["name"],
								"content"=>$wish["content"],
								"email"=>$wish["email"],
								"phone"=>$wish["phone"],
								"likecount"=>count(query($db,"SELECT*FROM `wishlike` WHERE `wishid`=?",[$wish["id"]]))
							];
						}

						usort($data,function($a,$b){
							$orderby=isset($_GET["orderby"])?$_GET["orderby"]:"id";
							$ordertype=isset($_GET["ordertype"])?$_GET["ordertype"]:"ASC";
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

						$maxpage=ceil(count($data)/$pagecount);

						for($i=$pagecount*$nowpage;$i<min($pagecount*($nowpage+1),count($data));$i=$i+1){
							$wish=$data[$i];
							?>
							<tr class="withtr" id="wish_<?= $wish["id"] ?>" data-content='<?= json_encode(query($db,"SELECT `content` FROM `wishlike` WHERE `wishid`=?",[$wish["id"]])) ?>'>
								<td class="center"><?= $i+1 ?></td>
								<td class="center"><?= $wish["name"] ?></td>
								<td><?= $wish["content"] ?></td>
								<td><?= $wish["email"] ?></td>
								<td><?= $wish["phone"] ?></td>
								<td class="likehover center" id="likecount_<?= $wish["id"] ?>" data-id="<?= $wish["id"] ?>">
									<?= count(query($db,"SELECT*FROM `wishlike` WHERE `wishid`=?",[$wish["id"]])) ?>
								</td>
							</tr>
							<?php
						}
					?>
				</table>
				<div class="center margin_10px_0px">
					<input type="button" class="button" onclick="location.href='<?= $prevlink ?>'" value="<" <?= $nowpage<=0?"disabled":"" ?>>
					<?= $nowpage+1 ?> / <?= $maxpage ?>
					<input type="button" class="button" onclick="location.href='<?= $nextlink ?>'" value=">" <?= $nowpage>=($maxpage-1)?"disabled":"" ?>>
				</div>
			</div>
		</main>

		<?php include "block/footer.php"; ?>

		<script src="initialize.js"></script>
		<script src="admin.js"></script>
	</body>
</html>