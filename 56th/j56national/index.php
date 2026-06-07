<?php include("link.php"); ?>
<!DOCTYPE html>
<html lang="zh-Hant">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>FunTech</title>
		<link rel="stylesheet" href="index.css">
	</head>
	<body id="home">
		<?php render_header(); ?>
		<main class="main">
			<section class="articles">
				<h2 class="section-title">文章列表</h2>
				<?php foreach(query("SELECT * FROM `article` ORDER BY `date` DESC") as $article){ ?>
					<article class="article-item">
						<h3 class="article-title"><?= e($article["title"]) ?></h3>
						<time datetime="<?= e($article["date"]) ?>" class="article-date"><?= e($article["date"]) ?></time>
						<p class="article-excerpt"><?= e($article["excerpt"]) ?></p>
						<a href="article.php?id=<?= e($article["id"]) ?>" class="article-readmore">閱讀更多</a>
					</article>
				<?php } ?>
			</section>
			<aside class="notifications">
				<h2 class="section-title">通知/公告</h2>
				<?php foreach(query("SELECT * FROM `notification` ORDER BY `date` DESC") as $notice){ ?>
					<div class="notification-item">
						<h3 class="notification-title"><?= e($notice["title"]) ?></h3>
						<time datetime="<?= e($notice["date"]) ?>" class="notification-date"><?= e($notice["date"]) ?></time>
					</div>
				<?php } ?>
			</aside>
		</main>
	</body>
</html>
