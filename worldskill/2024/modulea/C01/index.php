<!DOCTYPE html>
<html lang="zh-Hant">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Folder Zip 資料夾壓縮</title>
		<link rel="stylesheet" href="index.css">
	</head>
	<body>
		<div class="card">
			<h1>Folder Zip</h1>
			<p class="hint">選擇一個資料夾（不可為空），按下「壓縮」後會下載與資料夾同名的 zip 檔。</p>

			<form action="compress.php" method="POST" enctype="multipart/form-data" id="form">
				<input type="file" name="folder[]" id="folder" webkitdirectory mozdirectory directory required>
				<div class="selected" id="selected">尚未選擇資料夾</div>
				<button type="submit" id="submit" disabled>壓縮</button>
			</form>
		</div>
		<script src="index.js"></script>
	</body>
</html>
