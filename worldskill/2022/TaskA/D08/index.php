<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>RGB to HEX</title>
	</head>
	<body>
		<h4>RGB to HEX</h4>
		<form>
			<label for="red">Red:
				<input type="number" id="red" name="red" min="0" max="255" step="1" required>
			</label>

			<label for="green">Green:
				<input type="number" id="green" name="green" min="0" max="255" step="1" required>
			</label>

			<label for="blue">Blue:
				<input type="number" id="blue" name="blue" min="0" max="255" step="1" required>
			</label>

			<input type="submit" name="submit" value="submit">
		</form>

		<p>
			Hexadecimal: #
			<?php
				if(isset($_GET["submit"])){
					$red=$_GET["red"];
					$green=$_GET["green"];
					$blue=$_GET["blue"];

					$red=dechex($red); // 16進制
					if(strlen($red)<2){ $red="0".$red; } // 不足兩位補0

					$green=dechex($green);
					if(strlen($green)<2){ $green="0".$green; }

					$blue=dechex($blue);
					if(strlen($blue)<2){ $blue="0".$blue; }

					echo($red.$green.$blue);
				}
			?>
		</p>
	</body>
</html>