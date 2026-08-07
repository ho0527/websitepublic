<?php
	include "initialize.php";

	$key=$_GET["key"];

	if($key=="newdiary"){
		$name=trim($_POST["name"]??"");
		$email=trim($_POST["email"]??"");
		$location=trim($_POST["location"]??"");
		$date=trim($_POST["date"]??"");
		$rating=trim($_POST["rating"]??"");
		$content=trim($_POST["content"]??"");
		$photo=trim($_POST["photo"]??"");
		$error="";

		//後端再驗證一次，避免前端被略過
		if($name==""||$email==""||$location==""||$date==""||$rating==""||$content==""||$photo==""){
			$error="所有欄位皆為必填，請勿留空";
		}elseif(filter_var($email,FILTER_VALIDATE_EMAIL)==false){
			$error="Email 格式不正確，請輸入如 name@example.com";
		}elseif(intval($rating)<1||intval($rating)>5){
			$error="極光評分僅能填 1 至 5 之間的整數";
		}elseif(mb_strlen($content)<10){
			$error="觀賞心得至少需要 10 個字";
		}

		if($error!=""){
			?>
			<script>
				alert("<?= $error ?>");
				location.href="diary.php#postform";
			</script>
			<?php
		}else{
			query($db,"INSERT INTO `diary` (`name`,`email`,`location`,`date`,`rating`,`content`,`photo`,`createtime`) VALUES (?,?,?,?,?,?,?,?)",[$name,$email,$location,$date,intval($rating),$content,$photo,$time]);

			?>
			<script>
				alert("投稿成功");
				location.href="diary.php?success=1";
			</script>
			<?php
		}
	}elseif($key=="bless"){
		$id=$_GET["id"];

		query($db,"INSERT INTO `blessing` (`diaryid`,`createtime`) VALUES (?,?)",[$id,$time]);

		echo(json_encode([
			"success"=>true,
			"data"=>[
				"blesscount"=>count(query($db,"SELECT*FROM `blessing` WHERE `diaryid`=?",[$id]))
			]
		]));
	}elseif($key=="deletediary"){
		$id=$_GET["id"];

		query($db,"DELETE FROM `blessing` WHERE `diaryid`=?",[$id]);
		query($db,"DELETE FROM `diary` WHERE `id`=?",[$id]);

		?>
		<script>
			alert("刪除成功");
			location.href="admin.php";
		</script>
		<?php
	}else{
		?>
		<script>
			alert("key錯誤");
			location.href="index.php";
		</script>
		<?php
	}
?>
