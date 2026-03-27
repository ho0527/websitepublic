<?php
	include "initialize.php";

	$key=$_GET["key"];

	if($key=="newwish"){
		$name=$_POST["name"];
		$content=$_POST["content"];
		$email=$_POST["email"];
		$phone=$_POST["phone"];

		query($db,"INSERT INTO `wish` (`name`,`content`,`email`,`phone`) VALUES (?,?,?,?)",[$name,$content,$email,$phone]);

		?>
		<script>
			alert("新增成功");
			location.href="wish.php";
		</script>
		<?php
	}elseif($key=="like"){
		$id=$_GET["id"];
		$content=$_GET["content"];

		query($db,"INSERT INTO `wishlike` (`wishid`,`content`) VALUES (?,?)",[$id,$content]);

		echo(json_encode([
			"success"=>true,
			"data"=>[]
		]));
	}else{
		?>
		<script>
			alert("key錯誤");
			location.href="index.php";
		</script>
		<?php
	}
?>