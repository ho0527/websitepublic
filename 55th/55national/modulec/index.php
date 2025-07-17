<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>index listing</title>
		<link rel="stylesheet" href="/modulec/index.css">
        <meta name="description" content="summary">
        <meta name="twitter:card" content="summary">
        <meta name="twitter:title" content="小賀chris">
        <meta name="twitter:description" content="網頁">
        <meta name="twitter:image" content="favicon.ico">
        <meta property="og:title" content="小賀chris">
        <meta property="og:type" content="article">
        <meta property="og:image" content="favicon.ico">
        <meta property="og:url" content="/">
        <meta property="og:image:width" content="1280">
        <meta property="og:image:height" content="720">
        <meta property="og:description" content="網頁">
	</head>
	<body>
		<?php
			include("getfile.php");
			$path="content-pages/".(isset($_GET["data"])?($_GET["data"]."/"):"");
			if(is_dir($path)){
				?>
				<div class="main">
					<div class="index">
						<ul>
							<?php
							$keyword=$_GET["keyword"]??"";
							$folderlist=[];
							$filelist=[];

							function scan($dir){
								global $folderlist;
								global $filelist;
		
								if(is_dir($dir)&&$dh=opendir($dir)){
									while(($file=readdir($dh))!=false){
										if($file!="."&&$file!=".."&&$file!="images"){
											$filepath=$dir.$file;
											if(is_dir($filepath)){
												$folderlist[]=$filepath;
											}else{
												$filelist[]=$filepath;
											}
										}
									}
									closedir($dh);
								}
							}
		
							scan($path);
		
							sort($folderlist);
							rsort($filelist);
	
							$keywordlist=explode("/",$keyword);
	
							for($i=0;$i<count($folderlist);$i=$i+1){
								$filepath=$folderlist[$i];
								$link=explode("/",$filepath);
								$link=$link[count($link)-1];
								$keywordcheck=($keyword=="")?true:false;
								for($j=0;$j<count($keywordlist);$j=$j+1){
									if(strpos($link,$keywordlist[$j])){
										$keywordcheck=true;
										break;
									}
								}
								if($keywordcheck){
									?>
										<li class="indexli" onclick="location.href='/modulec/heritages/<?= substr($filepath,14); ?>?keyword=<?= $keyword ?>'">
											<div class="title"><?= $link ?></div>
										</li>
									<?php
								}
							}
		
							for($i=0;$i<count($filelist);$i=$i+1){
								$filepath=$filelist[$i];
								$data=getfile($filepath,$keyword);
								if($data!=false){
									if(strtolower($data["draft"])!="true"&&$data["date"]!=false&&strtotime($data["date"])<=strtotime($nowdate)){
										$link=explode("/",$filepath);
										$link=$link[count($link)-1];
										$temlink=explode(".",substr($filepath,14));
										array_pop($temlink);
										$link=implode(".",$temlink);
										?>
											<li class="indexli" onclick="location.href='/modulec/heritages/<?= $link ?>'">
												<!-- <div class="title"><?= ucwords(implode(" ",explode("-",substr(explode("/",$link)[count(explode("/",$link))-1],11)))) ?></div> -->
												<div class="title"><?= $data["title"] ?></div>
												<div class="summary"><?= $data["summary"] ?></div>
											</li>
										<?php
									}
								}
							}
							?>
						</ul>
					</div>
					<div class="search">
						<form action="">
							<input type="text" name="keyword" placeholder="KEYWORD">
							<input type="submit" value="submit">
						</form>
					</div>
				</div>
				<?php
			}else{
				$link=$_GET["data"];
				$data=explode("/",$_GET["data"]);
				$title=$data[count($data)-1];
				$folder="";

				if(2<=count($data)){
					array_pop($data);
					$folder=implode("/",$data);
				}

				$filelink="content-pages/".$link;

				if(file_exists($filelink.".html")){
					$type="html";
				}else if(file_exists($filelink.".txt")){
					$type="txt";
				}

				$data=getfile($filelink.".".$type);

				if($data!=false){
					?>
					<div class="header">
						<div class="cover" id="cover">
							<img src="/modulec/image/<?= $data["cover"] ?>" alt="cover" class="image">
	
							<div class="shadow" id="shadow"></div>
						</div>
						<div class="maintitle"><?= $data["title"] ?></div>
					</div>

					<div class="main heritagemain">
						<div class="index">
							<?php
								$detail=$data["detail"];
								if($type=="txt"){
									$tempdetail=explode("\n",$detail);
									$detail=[];
									for($i=0;$i<count($tempdetail);$i=$i+1){
										if($tempdetail[$i]!=""){
											$tempdata=explode(".",$tempdetail[$i]);
											if(array_pop($tempdata)=="jpg"){
												$detail[]="<img src='/modulec/image/".$data["cover"]."' alt='cover' class='image'>";
											}else{
												$detail[]="<p>".$tempdetail[$i]."</p>";
											}
										}
									}
									$detail=implode("",$detail);
								}

								echo($detail)
							?>
						</div>
						<div class="search">
							Date: <?= $data["date"] ?><br>
							Tag: <?php
								$taglist=explode(",",implode(",",explode(", ",$data["tags"])));
								for($i=0;$i<count($taglist);$i=$i+1){
									echo("<a href='/modulec/tags/".$taglist[$i]."' class='title'>".$taglist[$i]."</a> ");
								}
							?>
							<?php
								if($data["draft"]=="true"){
									echo("Draft: true");
								}
							?>
						</div>
					</div>

					<div class="lightbox" id="lightbox">
						<img src="" alt="big image" class="bigimage" id="bigimage">
					</div>

					<script src="/modulec/heritage.js"></script>
					<?php
				}else{
					header("location: /modulec/");
				}
			}
		?>
	</body>
</html>