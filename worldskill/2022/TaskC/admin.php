<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1.0">
        <title>admin list</title>
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

		<div>
			<table class="table-auto">
				<tr>
					<th>#</th>
					<th>username</th>
					<th>createtime</th>
					<th>lastlogintime</th>
				</tr>
				<?php
					$row=query($db,"SELECT * FROM `admin`");

					for($i=0;$i<count($row);$i=$i+1){
						?>
						<tr>
							<td><?= $i ?></td>
							<td><?= $row[$i]["username"] ?></td>
							<td><?= $row[$i]["createtime"] ?></td>
							<td><?= $row[$i]["lastlogintime"] ?></td>
						</tr>
						<?php
					}
				?>
			</table>
		</div>
    </body>
</html>