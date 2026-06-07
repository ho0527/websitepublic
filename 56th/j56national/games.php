<?php
	include("link.php");
	function load_games(){
		$games=[];
		foreach(glob(__DIR__."/games/*/game.json") as $path){
			$data=json_decode(file_get_contents($path),true);
			if(is_array($data)){
				$folder=basename(dirname($path));
				$games[]=[
					"id"=>$folder,
					"title"=>$data["title"] ?? $data["name"] ?? "Game ".$folder,
					"description"=>$data["description"] ?? "FunTech 小遊戲",
					"url"=>$data["entry"]["url"] ?? $data["url"] ?? "games/".$folder."/index.html",
					"scorePullUrl"=>$data["score"]["pullUrl"] ?? "api.php?key=score_pull",
					"cover"=>$data["cover"] ?? "",
				];
			}
		}
		if(!$games){
			$games=[
				["id"=>"demo","title"=>"FunTech Demo Game","description"=>"示範遊戲資料，可替換為 games/*/game.json。","url"=>"about:blank","scorePullUrl"=>"api.php?key=score_pull","cover"=>""],
			];
		}
		return $games;
	}
	$games=load_games();
	$current=$_GET["game"] ?? $games[0]["id"];
	$game=$games[0];
	foreach($games as $item){
		if($item["id"]==$current){ $game=$item; break; }
	}
?>
<!DOCTYPE html>
<html lang="zh-Hant">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>遊戲 - FunTech</title>
		<link rel="stylesheet" href="index.css">
	</head>
	<body id="games">
		<?php render_header(); ?>
		<main class="games-layout">
			<section class="games-section-game-list">
				<h1 class="section-title">遊戲列表</h1>
				<div class="game-list">
					<?php foreach($games as $item){ ?>
						<article class="game-item">
							<div class="game-cover"><?= $item["cover"] ? "<img src='".e($item["cover"])."' alt=''>" : "GAME" ?></div>
							<h2 class="game-title"><?= e($item["title"]) ?></h2>
							<p class="game-description"><?= e($item["description"]) ?></p>
							<a href="games.php?game=<?= e($item["id"]) ?>" class="play-game-link">開始遊戲</a>
						</article>
					<?php } ?>
				</div>
			</section>
			<section class="game-play section-game-play">
				<h2 class="current-game-title"><?= e($game["title"]) ?></h2>
				<section class="game-area section-game-area">
					<iframe src="<?= e($game["url"]) ?>" title="<?= e($game["title"]) ?>" class="game-frame iframe-game-frame"></iframe>
				</section>
			</section>
			<aside class="game-leaderboard aside-game-leaderboard" data-score-url="<?= e($game["scorePullUrl"]) ?>">
				<h2 class="leaderboard-title game-leaderboard-leaderboard-title">排行榜</h2>
				<ol class="leaderboard-list"></ol>
			</aside>
		</main>
		<script>
			const board=document.querySelector(".game-leaderboard");
			const list=document.querySelector(".leaderboard-list");
			fetch(board.dataset.scoreUrl)
				.then((response)=>response.json())
				.then((rows)=>{
					list.innerHTML=rows.map((row,index)=>`
						<li class="leaderboard-item">
							<span class="leaderboard-item-player-rank">${index+1}</span>
							<span class="leaderboard-item-player-name">${row["玩家名稱"] ?? row.name ?? "玩家"}</span>
							<strong>${row["分數"] ?? row.score ?? 0}</strong>
						</li>
					`).join("");
				})
				.catch(()=>{ list.innerHTML="<li class='leaderboard-item'>目前尚無分數紀錄</li>"; });
		</script>
	</body>
</html>
