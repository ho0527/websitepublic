<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1.0">
        <title>user detail</title>
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

		<div class="main center textcenter">
			<?php
				$username=$_GET["username"];
				$row=query($db,"SELECT * FROM `user` WHERE `username`=? AND `blocktime` IS NULL",[$username]);

				if(isset($row[0])){
					$row=$row[0];
					?>
					<div>
						<table class="table-auto textcenter width-75vw">
							<tr>
								<th>username</th>
								<th>createtime</th>
								<th>lastlogintime</th>
							</tr>
							<tr>
								<td><?= $row["username"] ?></td>
								<td><?= $row["createtime"] ?></td>
								<td><?= $row["lastlogintime"] ?></td>
							</tr>
						</table>

						<hr>

						<form method="POST">
							<select class="select" name="blockreason">
								<option value="You have been blocked by an administrator">You have been blocked by an administrator</option>
								<option value="You have been blocked for spamming">You have been blocked for spamming</option>
								<option value="You have been blocked for cheating">You have been blocked for cheating</option>
							</select>
							<input type="hidden" name="userid" value="<?= $row["id"]; ?>">
							<input type="submit" class="button outline" name="blocksubmit" value="submit">
						</form>
					</div>
					<?php
				}else{
					echo("user not found");
				}
			?>
		</div>

		<?php
			if(isset($_POST["blocksubmit"])){
				$id=$_POST["userid"];

				if($id){
					$row=query($db,"SELECT*FROM `user` WHERE `id`=?",[$id]);
					if($row){
						query($db,"UPDATE `user` SET `blocktime`=?,`blockreason`=? WHERE `id`=?",[$time,$_POST["blockreason"],$id]);
					}
				}

				header("location: user.php");
			}
		?>
    </body>
</html>