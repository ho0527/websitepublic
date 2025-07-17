<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>index listing</title>
		<link rel="stylesheet" href="../index.css">
	</head>
	<body>
		<div class="main">
			<div class="index" style="width: 100%">
				<ul>
					<?php
						include("getfile.php");
	
						$path="content-pages/".($_GET["path"]??"");
						$tag=$_GET["tag"]??"";
						$filelist=[];
	
						function scan($dir){
							global $rootlink;
							global $filelist;

							if (is_dir($dir)){
								if ($dh = opendir($dir)){
									while (($file = readdir($dh))!= false){
										if( $file!="." && $file!=".." && $file!="images"){
											//文件名的全路径 包含文件名
											$filepath = $dir.$file;
											// echo $filepath."<br>";
											if(is_dir($filepath)){
												scan($filepath."/");
											}else{
												$filelist[]=$filepath;
											}
										}
									}
									closedir($dh);
								}
							}
						}
	
						scan($path);
	
						rsort($filelist);

	
						for($i=0;$i<count($filelist);$i=$i+1){
							$filepath=$filelist[$i];
							$data=getfile($filepath);
							if($data!=false){
								$taglist=explode(",",implode(",",explode(", ",$data["tags"])));
								if(
									strtolower($data["draft"])!="true"&&$data["date"]!=false&&strtotime($data["date"])<=strtotime($nowdate)&&
									in_array($tag,array_map("trim",$taglist))
								){
									$link=explode("/",$filepath);
									$link=$link[count($link)-1];
									$temlink=explode(".",substr($filepath,14));
									array_pop($temlink);
									$link=implode(".",$temlink);
									?>
										<li class="indexli" onclick="location.href='/modulec/heritages/<?= $link ?>'">
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
		</div>
	</body>
</html>