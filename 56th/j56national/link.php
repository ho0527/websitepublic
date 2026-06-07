<?php
	session_start();
	date_default_timezone_set("Asia/Taipei");
	$time=date("Y-m-d H:i:s");

	$db=new PDO("mysql:host=127.0.0.1;dbname=56jnational;charset=utf8mb4","root","");
	$db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);

	function query(string $sql,$data=[]){
		global $db;
		$p=$db->prepare($sql);
		$p->execute($data);
		if(preg_match("/^\s*(SELECT|SHOW|DESCRIBE|PRAGMA)/i",$sql)){
			return $p->fetchAll();
		}
		return $p->rowCount();
	}

	function init_database(){
		query("CREATE TABLE IF NOT EXISTS `user`(
			`id` INT AUTO_INCREMENT PRIMARY KEY,
			`username` VARCHAR(60) NOT NULL UNIQUE,
			`email` VARCHAR(120) NOT NULL,
			`password` VARCHAR(255) NOT NULL,
			`avatar` VARCHAR(255) DEFAULT '',
			`bio` TEXT,
			`created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
		) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
		query("CREATE TABLE IF NOT EXISTS `article`(
			`id` INT AUTO_INCREMENT PRIMARY KEY,
			`article_id` INT NULL,
			`user_id` INT NULL,
			`title` VARCHAR(160) NOT NULL,
			`date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			`excerpt` TEXT,
			`content` TEXT NOT NULL
		) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
		query("CREATE TABLE IF NOT EXISTS `notification`(
			`id` INT AUTO_INCREMENT PRIMARY KEY,
			`title` VARCHAR(160) NOT NULL,
			`date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
		) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
		query("CREATE TABLE IF NOT EXISTS `friend_request`(
			`id` INT AUTO_INCREMENT PRIMARY KEY,
			`sender_id` INT NOT NULL,
			`receiver_id` INT NOT NULL,
			`status` ENUM('pending','accepted','rejected') NOT NULL DEFAULT 'pending',
			`created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			UNIQUE KEY `uniq_request` (`sender_id`,`receiver_id`)
		) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
		query("CREATE TABLE IF NOT EXISTS `friendship`(
			`id` INT AUTO_INCREMENT PRIMARY KEY,
			`user_id` INT NOT NULL,
			`friend_id` INT NOT NULL,
			`created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			UNIQUE KEY `uniq_friendship` (`user_id`,`friend_id`)
		) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

		if(!query("SELECT `id` FROM `article` LIMIT 1")){
			query("INSERT INTO `article`(`article_id`,`user_id`,`title`,`date`,`excerpt`,`content`) VALUES
				(1,NULL,'FunTech 改版公告',NOW(),'FunTech 社群網站完成首頁、文章、個人頁與好友系統更新。','FunTech 希望讓青少年在安全且有趣的環境中探索科技、分享作品與挑戰小遊戲。這次改版重新整理了資訊架構，讓文章、公告與個人頁更容易瀏覽。'),
				(2,NULL,'平台遊戲挑戰開放',NOW(),'遊戲頁面支援嵌入指定遊戲與讀取排行榜資料。','玩家可以在遊戲列表中選擇不同遊戲，進入遊戲後查看目前分數排行榜。若遊戲資料夾提供 game.json，系統會自動載入遊戲資訊。')");
		}
		if(!query("SELECT `id` FROM `notification` LIMIT 1")){
			query("INSERT INTO `notification`(`title`,`date`) VALUES
				('歡迎加入 FunTech 社群',NOW()),
				('個人頁與好友功能已開放使用',NOW()),
				('遊戲排行榜資料將依各遊戲 API 顯示',NOW())");
		}
	}

	function e($value){
		return htmlspecialchars((string)$value,ENT_QUOTES,"UTF-8");
	}

	function current_user(){
		if(!isset($_SESSION["userid"])){
			return null;
		}
		$row=query("SELECT * FROM `user` WHERE `id`=?",[$_SESSION["userid"]]);
		return $row ? $row[0] : null;
	}

	function signed_in(){
		return isset($_SESSION["signin"]) && $_SESSION["signin"]===true;
	}

	function require_login(){
		if(!signed_in()){
			header("location: signin.php");
			exit;
		}
	}

	function render_header(){
		$user=current_user();
		?>
		<header class="site-header">
			<div class="brand">
				<a href="index.php" class="brand-link">FunTech</a>
			</div>
			<nav class="main-nav">
				<a href="index.php" class="home-link">首頁</a>
				<a href="games.php" class="games-link">遊戲</a>
				<a href="friends.php" class="friends-link">好友</a>
			</nav>
			<div class="user-area">
				<?php if($user){ ?>
					<img src="<?= e(avatar_url($user)) ?>" alt="<?= e($user["username"]) ?>" class="user-badge">
					<a href="profile.php" class="profile-link">個人頁面</a>
					<a href="api.php?key=signout" class="logout-link">登出</a>
				<?php }else{ ?>
					<a href="signin.php" class="login-link">登入</a>
					<a href="signup.php" class="register-link">註冊</a>
				<?php } ?>
			</div>
		</header>
		<?php
	}

	function avatar_url($user){
		if(!empty($user["avatar"])){
			return $user["avatar"];
		}
		$name=mb_substr($user["username"] ?? "F",0,1,"UTF-8");
		$svg="<svg xmlns='http://www.w3.org/2000/svg' width='96' height='96' viewBox='0 0 96 96'><rect width='96' height='96' rx='48' fill='%230e7490'/><text x='48' y='58' text-anchor='middle' font-size='36' font-family='Arial,sans-serif' font-weight='700' fill='white'>".e($name)."</text></svg>";
		return "data:image/svg+xml;charset=UTF-8,".rawurlencode($svg);
	}

	function article_by_id($id){
		$row=query("SELECT * FROM `article` WHERE `id`=? OR `article_id`=? LIMIT 1",[$id,$id]);
		return $row ? $row[0] : null;
	}

	init_database();
?>
