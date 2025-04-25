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
		<?php
			if(isset($_GET["slug"])){
				$slug=$_GET["slug"];
				$row=query($db,"SELECT*FROM `game` WHERE `slug` LIKE ? AND `deletetime` IS NULL",[$slug]);
				if(0<count($row)){
					$row=$row[0];
				}else{
					header("location: game.php");
				}
			}
		?>

		<div class="navigationbar">
            <div class="navigationbarleft"></div>
            <div class="navigationbarright" id="navigationbarright">
				<?php include("navigationbarright.php"); ?>
			</div>
		</div>

		<div class="main center width-75vw">
			<table class="table-auto">
				<tr>
					<th>#</th>
					<th>title</th>
					<th>description</th>
					<th>author</th>
					<th>function</th>
				</tr>
				<tr>
					<td><?= $i ?></td>
					<td><?= $row["title"] ?></td>
					<td><?= $row["description"] ?></td>
					<td><?= query($db,"SELECT*FROM `user` WHERE `id`=?",[$row["userid"]])[0]["username"] ?></td>
					<td>
						<a href="api/deletegame.php?slug=<?= $row["slug"] ?>" class="button outline">delete</a>
					</td>
				</tr>
			</table>

			<div>
				score<br>
				<table class="table-auto">
					<tr>
						<th>#</th>
						<th>user name</th>
						<th>game name</th>
						<th>version</th>
						<th>score</th>
					</tr>
					<?php
						$gameversionrow=query($db,"SELECT*FROM `gameversion` WHERE `gameid`=?",[$row["id"]]);

						for($i=0;$i<count($gameversionrow);$i=$i+1){
							$scorerow=query($db,"SELECT*FROM `score` WHERE `gameversionid`=?",[$gameversionrow[$i]["id"]]);
							for($j=0;$j<count($scorerow);$j=$j+1){
								$userrow=query($db,"SELECT*FROM `user` WHERE `id`=?",[$scorerow[$j]["userid"]])[0];
								?>
								<tr>
									<td><?= $i*$j+$j ?></td>
									<td><?= $userrow["username"] ?></td>
									<td><?= $row["title"] ?></td>
									<td><?= $gameversionrow[$i]["version"] ?></td>
									<td><?= $scorerow[$j]["score"] ?></td>
									<td>
										<a href="api/deletescore.php?id=<?= $scorerow[$j]["id"] ?>" class="button outline">delete score</a>
										<a href="api/deleteuserscore.php?id=<?= $scorerow[$j]["id"] ?>&userid=<?= $userrow["id"] ?>" class="button outline">delete user socre</a>
									</td>
								</tr>
								<?php
							}
						}
					?>
				</table>
			</div>
		</div>
    </body>
</html>