<?php
	include("link.php");
	$key=$_GET["key"] ?? "";

	function back($fallback="index.php"){
		header("location: ".($_SERVER["HTTP_REFERER"] ?? $fallback));
		exit;
	}

	function alert_back($message,$fallback="index.php"){
		?>
		<script>
			alert(<?= json_encode($message) ?>);
			location.href=<?= json_encode($fallback) ?>;
		</script>
		<?php
		exit;
	}

	if($key=="signin"){
		$row=query("SELECT * FROM `user` WHERE `username`=? AND `password`=?",[$_POST["username"] ?? "",$_POST["password"] ?? ""]);
		if($row){
			$user=$row[0];
			$_SESSION["signin"]=true;
			$_SESSION["userid"]=$user["id"];
			$_SESSION["username"]=$user["username"];
			header("location: profile.php");
			exit;
		}
		alert_back("登入失敗，請檢查帳號密碼是否正確","signin.php");
	}elseif($key=="signup"){
		$username=trim($_POST["username"] ?? "");
		$email=trim($_POST["email"] ?? "");
		$password=$_POST["password"] ?? "";
		$confirm=$_POST["password-confirm"] ?? "";
		if($username=="" || $email=="" || $password==""){
			alert_back("請填寫完整資料","signup.php");
		}
		if($password!==$confirm){
			alert_back("密碼與確認密碼不一致","signup.php");
		}
		if(query("SELECT `id` FROM `user` WHERE `username`=?",[$username])){
			alert_back("帳號已存在","signup.php");
		}
		query("INSERT INTO `user`(`username`,`email`,`password`,`bio`) VALUES (?,?,?,?)",[$username,$email,$password,""]);
		alert_back("註冊成功，請重新登入","signin.php");
	}elseif($key=="signout"){
		session_destroy();
		header("location: index.php");
		exit;
	}elseif($key=="update_profile"){
		require_login();
		$bio=trim($_POST["bio"] ?? "");
		query("UPDATE `user` SET `bio`=? WHERE `id`=?",[$bio,$_SESSION["userid"]]);
		back("profile.php");
	}elseif($key=="create_article"){
		require_login();
		$title=trim($_POST["title"] ?? "");
		$content=trim($_POST["content"] ?? "");
		if($title=="" || $content==""){
			alert_back("文章標題與內容不可空白","profile.php");
		}
		$excerpt=mb_substr(strip_tags($content),0,80,"UTF-8");
		query("INSERT INTO `article`(`user_id`,`title`,`date`,`excerpt`,`content`) VALUES (?,?,?,?,?)",[$_SESSION["userid"],$title,date("Y-m-d H:i:s"),$excerpt,$content]);
		back("profile.php");
	}elseif($key=="friend_search"){
		require_login();
		$keyword="%".trim($_GET["q"] ?? $_POST["q"] ?? "")."%";
		header("Content-Type: application/json; charset=utf-8");
		echo json_encode(query("SELECT `id`,`username`,`avatar`,`bio` FROM `user` WHERE `id`<>? AND `username` LIKE ? ORDER BY `username`",[$_SESSION["userid"],$keyword]),JSON_UNESCAPED_UNICODE);
		exit;
	}elseif($key=="send_friend_request"){
		require_login();
		$receiver=(int)($_POST["receiver_id"] ?? $_GET["receiver_id"] ?? 0);
		if($receiver>0 && $receiver!=$_SESSION["userid"]){
			query("INSERT IGNORE INTO `friend_request`(`sender_id`,`receiver_id`) VALUES (?,?)",[$_SESSION["userid"],$receiver]);
		}
		back("friends.php");
	}elseif($key=="accept_friend_request"){
		require_login();
		$id=(int)($_POST["request_id"] ?? $_GET["request_id"] ?? 0);
		$row=query("SELECT * FROM `friend_request` WHERE `id`=? AND `receiver_id`=? AND `status`='pending'",[$id,$_SESSION["userid"]]);
		if($row){
			$request=$row[0];
			query("UPDATE `friend_request` SET `status`='accepted' WHERE `id`=?",[$id]);
			query("INSERT IGNORE INTO `friendship`(`user_id`,`friend_id`) VALUES (?,?)",[$request["sender_id"],$request["receiver_id"]]);
			query("INSERT IGNORE INTO `friendship`(`user_id`,`friend_id`) VALUES (?,?)",[$request["receiver_id"],$request["sender_id"]]);
		}
		back("friends.php");
	}elseif($key=="reject_friend_request"){
		require_login();
		$id=(int)($_POST["request_id"] ?? $_GET["request_id"] ?? 0);
		query("UPDATE `friend_request` SET `status`='rejected' WHERE `id`=? AND `receiver_id`=?",[$id,$_SESSION["userid"]]);
		back("friends.php");
	}elseif($key=="remove_friend"){
		require_login();
		$friend=(int)($_POST["friend_id"] ?? $_GET["friend_id"] ?? 0);
		query("DELETE FROM `friendship` WHERE (`user_id`=? AND `friend_id`=?) OR (`user_id`=? AND `friend_id`=?)",[$_SESSION["userid"],$friend,$friend,$_SESSION["userid"]]);
		back("friends.php");
	}elseif($key=="score_pull"){
		header("Content-Type: application/json; charset=utf-8");
		echo json_encode([
			["玩家名稱"=>"玩家 1","分數"=>95000],
			["玩家名稱"=>"玩家 2","分數"=>89000],
			["玩家名稱"=>"玩家 3","分數"=>82500],
			["玩家名稱"=>"玩家 4","分數"=>82000],
			["玩家名稱"=>"玩家 5","分數"=>70500],
		],JSON_UNESCAPED_UNICODE);
		exit;
	}else{
		header("location: index.php");
		exit;
	}
?>
