<?php
/**
 * 後台頁面共用版型
 *
 * @var string $pageTitle   瀏覽器標題
 * @var string $pageContent 已算繪好的內容 HTML
 */
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title><?= h($pageTitle) ?></title>
		<link rel="stylesheet" href="<?= h(assetUrl('index.css')) ?>">
	</head>
	<body>
<?= $pageContent ?>
	</body>
</html>
