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
			<table class="table-auto">
				<tr>
					<th>#</th>
					<th>user name</th>
					<th>game name</th>
					<th>version</th>
					<th>score</th>
				</tr>
				<?php
					$row=query($db,"SELECT*FROM `score`",[]);

					for($i=0;$i<count($row);$i=$i+1){
						$userrow=query($db,"SELECT*FROM `user` WHERE `id`=?",[$row[$i]["userid"]])[0];
						$gameversionrow=query($db,"SELECT*FROM `gameversion` WHERE `id`=?",[$row[$i]["gameversionid"]])[0];
						$gamenrow=query($db,"SELECT*FROM `game` WHERE `id`=?",[$gameversionrow["gameid"]])[0];
						?>
						<tr>
							<td><?= $i ?></td>
							<td><?= $userrow["username"] ?></td>
							<td><?= $gamenrow["title"] ?></td>
							<td><?= $gameversionrow["version"] ?></td>
							<td><?= $row[$i]["score"] ?></td>
						</tr>
						<?php
					}
				?>
			</table>
		</div>
    </body>
</html>