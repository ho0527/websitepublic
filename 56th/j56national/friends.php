<?php
	include("link.php");
	require_login();
	$user=current_user();
	$keyword=trim($_GET["q"] ?? "");
	$results=[];
	if($keyword!=""){
		$results=query("SELECT `id`,`username`,`avatar`,`bio` FROM `user` WHERE `id`<>? AND `username` LIKE ? ORDER BY `username`",[$user["id"],"%".$keyword."%"]);
	}
	$friends=query("SELECT u.* FROM `friendship` f JOIN `user` u ON u.`id`=f.`friend_id` WHERE f.`user_id`=? ORDER BY u.`username`",[$user["id"]]);
	$incoming=query("SELECT r.*,u.`username`,u.`avatar` FROM `friend_request` r JOIN `user` u ON u.`id`=r.`sender_id` WHERE r.`receiver_id`=? AND r.`status`='pending' ORDER BY r.`created_at` DESC",[$user["id"]]);
	$sent=query("SELECT r.*,u.`username`,u.`avatar` FROM `friend_request` r JOIN `user` u ON u.`id`=r.`receiver_id` WHERE r.`sender_id`=? AND r.`status`='pending' ORDER BY r.`created_at` DESC",[$user["id"]]);
?>
<!DOCTYPE html>
<html lang="zh-Hant">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>好友 - FunTech</title>
		<link rel="stylesheet" href="index.css">
	</head>
	<body id="friends-page">
		<?php render_header(); ?>
		<main class="friends-layout">
			<section class="friend-search-section friends-page-friend-search-section">
				<h1 class="section-title">搜尋使用者</h1>
				<form action="friends.php" method="GET" class="friend-search-form form-friend-search-form">
					<input type="text" name="q" value="<?= e($keyword) ?>" class="friend-search-input input-friend-search-input" placeholder="輸入使用者名稱">
					<button type="submit" class="friend-search-submit button-friend-search-submit">搜尋</button>
				</form>
				<div class="search-result-list friend-search-section-search-result-list">
					<?php foreach($results as $result){ ?>
						<article class="search-result-item search-result-list-search-result-item">
							<div>
								<h2 class="result-username"><?= e($result["username"]) ?></h2>
								<p><?= e($result["bio"] ?: "尚未填寫自我介紹") ?></p>
							</div>
							<a href="profile.php?user=<?= e($result["id"]) ?>" class="view-profile-link search-result-item-a-view-profile-link">查看</a>
							<form action="api.php?key=send_friend_request" method="POST">
								<input type="hidden" name="receiver_id" value="<?= e($result["id"]) ?>">
								<button type="submit">加好友</button>
							</form>
						</article>
					<?php } ?>
				</div>
			</section>

			<section class="friend-list-section">
				<h2 class="section-title">我的好友</h2>
				<?php foreach($friends as $friend){ ?>
					<article class="friend-item friend-list-section-friend-item">
						<img src="<?= e(avatar_url($friend)) ?>" alt="" class="friend-avatar img-friend-avatar">
						<span class="friend-name"><?= e($friend["username"]) ?></span>
						<form action="api.php?key=remove_friend" method="POST">
							<input type="hidden" name="friend_id" value="<?= e($friend["id"]) ?>">
							<button type="submit" class="remove-friend-button">移除</button>
						</form>
					</article>
				<?php } ?>
			</section>

			<section class="incoming-requests-section">
				<h2 class="section-title">收到的好友申請</h2>
				<?php foreach($incoming as $request){ ?>
					<article class="request-item incoming-requests-section-request-item">
						<img src="<?= e(avatar_url($request)) ?>" alt="" class="request-avatar img-request-avatar">
						<span class="request-username"><?= e($request["username"]) ?></span>
						<form action="api.php?key=accept_friend_request" method="POST">
							<input type="hidden" name="request_id" value="<?= e($request["id"]) ?>">
							<button type="submit" class="accept-request-button button-accept-request-button">接受</button>
						</form>
						<form action="api.php?key=reject_friend_request" method="POST">
							<input type="hidden" name="request_id" value="<?= e($request["id"]) ?>">
							<button type="submit" class="reject-request-button button-reject-request-button">拒絕</button>
						</form>
					</article>
				<?php } ?>
			</section>

			<section class="sent-requests-section">
				<h2 class="section-title">我送出的好友申請</h2>
				<?php foreach($sent as $request){ ?>
					<article class="request-item sent-requests-section-request-item">
						<img src="<?= e(avatar_url($request)) ?>" alt="" class="request-avatar img-request-avatar">
						<span class="request-username"><?= e($request["username"]) ?></span>
						<span>等待回覆</span>
					</article>
				<?php } ?>
			</section>
		</main>
	</body>
</html>
