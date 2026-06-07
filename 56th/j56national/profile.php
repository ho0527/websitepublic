<?php
	include("link.php");
	require_login();
	$current_user=current_user();
	$view_id=(int)($_GET["user"] ?? $current_user["id"]);
	$row=query("SELECT * FROM `user` WHERE `id`=?",[$view_id]);
	$user=$row ? $row[0] : $current_user;
	$is_own_page=$user["id"]==$current_user["id"];
	$articles=query("SELECT * FROM `article` WHERE `user_id`=? ORDER BY `date` DESC",[$user["id"]]);
?>
<!DOCTYPE html>
<html lang="zh-Hant">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>個人頁面 - FunTech</title>
		<link rel="stylesheet" href="index.css">
	</head>
	<body id="profile-page">
		<?php render_header(); ?>
		<main class="profile-layout">
			<section class="profile-header">
				<img src="<?= e(avatar_url($user)) ?>" alt="<?= e($user["username"]) ?>" class="profile-avatar img-profile-avatar">
				<div>
					<h1 class="profile-username"><?= e($user["username"]) ?></h1>
					<p class="profile-bio"><?= e($user["bio"] ?: "尚未填寫自我介紹") ?></p>
				</div>
			</section>

			<?php if($is_own_page){ ?>
				<section class="profile-editor">
					<h2>編輯個人資料</h2>
					<form action="api.php?key=update_profile" method="POST" class="profile-form">
						<textarea name="bio" class="profile-bio-input textarea-profile-bio-input" rows="4" placeholder="輸入自我介紹"><?= e($user["bio"]) ?></textarea>
						<button type="submit">更新資料</button>
					</form>
				</section>

				<section class="article-create">
					<h2>發表文章</h2>
					<form action="api.php?key=create_article" method="POST" class="article-create-form form-article-create-form">
						<input type="text" name="title" class="article-title-input input-article-title-input" placeholder="文章標題" required>
						<textarea name="content" class="article-content-input textarea-article-content-input" rows="6" placeholder="文章內容" required></textarea>
						<button type="submit" class="article-submit-button button-article-submit-button">發表文章</button>
					</form>
				</section>
			<?php } ?>

			<section class="profile-articles section-profile-articles">
				<h2>我的文章</h2>
				<?php if($articles){ ?>
					<?php foreach($articles as $article){ ?>
						<article class="article-item">
							<h3 class="article-title"><?= e($article["title"]) ?></h3>
							<time datetime="<?= e($article["date"]) ?>" class="article-date"><?= e($article["date"]) ?></time>
							<p class="article-excerpt"><?= e($article["excerpt"]) ?></p>
							<a href="article.php?id=<?= e($article["id"]) ?>" class="article-readmore">閱讀更多</a>
						</article>
					<?php } ?>
				<?php }else{ ?>
					<p class="empty-article-message profile-articles-empty-article-message">目前尚無文章</p>
				<?php } ?>
			</section>
		</main>
	</body>
</html>
