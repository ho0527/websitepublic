<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Document</title>
		<link rel="stylesheet" href="index.css">
	</head>
	<body id="article">
		<header class="site-header">
			<div class="brand">
				<a href="" class="brand-link">
					<img src="" alt="" class="brand">
				</a>
			</div>
			<nav class="main-nav">
				<a href="" class="home-link">首頁</a>
				<a href="" class="games-link">遊戲</a>
				<a href="" class="friends-link">好友</a>
			</nav>
			<div class="user-area">
				<?php
					include("link.php");

					if(isset($_SESSION["signin"])){
						?>
						<img src="" alt="" class="user-badge">
						<a href="" class="profile-link">個人頁面</a>
						<a href="" class="logout-link">登出</a>
						<?php
					}else{
						?>
						<a href="signin.php" class="login-link">登入</a>
						<a href="" class="register-link">註冊</a>
						<?php
					}
				?>
			</div>
		</header>
		<div class="main">
			<section class="articles">
				<h2 class="section-title">文章列表</h2>
				<?php
					$row=query("SELECT*FROM `article`");
					for($i=0;$i<count($row);$i=$i+1){
						?>
						<article class="article-item">
							<div class="article-title"><?= $row[$i]["title"] ?></div>
							<time datetime="<?= $row[$i]["date"] ?>" class="article-date"><?= $row[$i]["date"] ?></time>
							<div class="article-excerpt"><?= $row[$i]["excerpt"] ?></div>
							<a href="article.php?id=<?= $row[$i]["id"] ?>" class="article-readmore">閱讀更多</a>
						</article>
						<?php
					}
				?>
			</section>
			<aside class="notifications">
				<h2 class="section-title">通知/公告</h2>
				<?php
					// $notificationrow=query("SELECT*FROM `notification`");
					// for($i=0;$i<count($notificationrow);$i=$i+1){
					// 	?>
					<!-- // 	<aside class="notification-item">
					// 		<div class="notification-title"></div>
					// 		<time datetime="" class="notification-date"></time>
					// 	</aside> -->
					// 	<?php
					// }
				?>
			</aside>
		</div>
	</body>
</html>