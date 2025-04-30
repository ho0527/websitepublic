<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Folder Zip</title>
	</head>
	<body>
		<form action="compress.php" method="POST" enctype="multipart/form-data">
			<input type="file" name="folder[]" webkitdirectory directory required>
			<button type="submit">壓縮</button>
		</form>
	</body>
</html>