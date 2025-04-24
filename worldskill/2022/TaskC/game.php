<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1.0">
        <title>game list</title>
        <link rel="stylesheet" href="/index.css">
        <link rel="stylesheet" href="index.css">
		<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        <link rel="stylesheet" href="/chrisplugin/css/chrisplugin.css">
        <script src="/chrisplugin/js/chrisplugin.js"></script>
    </head>
    <body>
		<?php include("link.php");include("signincheck.php"); ?>

		<div class="navigationbar">
            <div class="navigationbarleft"></div>
            <div class="navigationbarright" id="navigationbarright">
				<?php include("navigationbarright.php"); ?>
				<a href="api/deleteallscore.php" class="navigationbarbutton">delete all score</a>
			</div>
		</div>

		<div class="main center width-75vw">
			<form>
				<div class="input">
					<input type="text" name="title" value="value">
				<div>
				<input type="submit" class="button" name="submit" value="submit">
			</form>
			<hr>
			<table class="table-auto">
				<tr>
					<th>#</th>
					<th>title</th>
					<th>description</th>
					<th>thumbnail</th>
					<th>author</th>
					<th>function</th>
				</tr>
				<?php
					$row=query($db,"SELECT*FROM `game` WHERE `title` LIKE ?",["%".($_GET["title"]??"")."%"]);

					for($i=0;$i<count($row);$i=$i+1){
						$gameversionrow=query($db,"SELECT*FROM `gameversion` WHERE `gameid`=?",[$row[$i]["id"]]);
						$gameversionrow=$gameversionrow[count($gameversionrow)-1]["version"];
						?>
						<tr>
							<td><?= $i ?></td>
							<td><?= $row[$i]["title"] ?></td>
							<td><?= $row[$i]["description"] ?></td>
							<td><?php if($gameversionrow["thumbnailpath"]){ echo("no thumbnail"); }else{ ?><img src="<?= $gameversionrow["thumbnailpath"] ?>" class="image"><?php } ?></td>
							<td><?= query($db,"SELECT*FROM `user` WHERE `id`=?",[$row[$i]["userid"]])[0]["username"] ?></td>
							<td>
								<?php
									if($row[$i]["deletetime"]==null){
										?><a href="gamedetail.php?slug=<?= $row[$i]["slug"] ?>" class="button outline">see</a><?php
									}else{
										?>deleted<?php
									}
								?>
							</td>
						</tr>
						<?php
					}
				?>
			</table>
		</div>
    </body>
</html>