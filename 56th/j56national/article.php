<?php
	include("link.php");
	$article=article_by_id($_GET["id"] ?? 0);
	if(!$article){ header("location: index.php"); exit; }
?>
<!DOCTYPE html>
<html lang="zh-Hant">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title><?= e($article["title"]) ?> - FunTech</title>
		<link rel="stylesheet" href="index.css">
	</head>
	<body id="article">
		<?php render_header(); ?>
		<main class="article-page">
			<header class="article-header">
				<h1 class="article-title"><?= e($article["title"]) ?></h1>
				<time datetime="<?= e($article["date"]) ?>" class="article-date"><?= e($article["date"]) ?></time>
			</header>
			<section class="article-body"><?= nl2br(e($article["content"])) ?></section>
		</main>
	</body>
</html>
