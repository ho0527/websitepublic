<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Document</title>
		<link rel="stylesheet" href="index.css">
	</head>
	<body id="article">
		<?php
			include("link.php");
			$id=$_GET["id"];

			if($row=query("SELECT*FROM `article` WHERE `article_id`=?",[$id])){
				$row=$row[0];
				?>
				<header class="article-header">
					<h1 class="article-title"></h1>
					<time datetime="" class="article-date"></time>
				</header>
				<section class="article-body"></section>
				<?php
			}else{
				header("location: index.php");
			}
		?>
	</body>
</html>