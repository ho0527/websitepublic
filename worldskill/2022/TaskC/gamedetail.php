<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1.0">
        <title>game detail</title>
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
			</div>
		</div>

		<?php
			$slug=$_GET["slug"];
			$row=query($db,"SELECT * FROM `game` WHERE `slug`=?",[$slug]);

			if(isset($row[0])){
				?>
				<div>
					<table class="table-auto">
						<tr>
							<th>#</th>
							<th>username</th>
							<th>createtime</th>
							<th>lastlogintime</th>
							<th>function</th>
						</tr>
						<?php
							$row=query($db,"SELECT * FROM `user`");

							for($i=0;$i<count($row);$i=$i+1){
								?>
								<tr>
									<td><?= $i ?></td>
									<td><?= $row[$i]["username"] ?></td>
									<td><?= $row[$i]["createtime"] ?></td>
									<td><?= $row[$i]["lastlogintime"] ?></td>
									<td>
										<?php
											if($row[$i]["deletetime"]){
												?><a href="userdetail.php?id=<?= $row[$i]["id"] ?>" class="button outline">see</a><?php
											}else{
												?><a href="api/unblock.php?id=<?= $row[$i]["id"] ?>" class="button outline">unblock</a><?php
											}
										?>
									</td>
								</tr>
								<?php
							}
						?>
					</table>
				</div>
				<?php
			}else{
				echo("user not found");
			}
		?>
    </body>
</html>