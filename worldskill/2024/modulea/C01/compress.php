<?php
	if(empty($_FILES["folder"])) {
		exit("No folder uploaded");
	}

	$tmpdir="upload_".time();
	mkdir($tmpdir);

	foreach($_FILES["folder"]["name"] as $i=>$name){
		if($_FILES["folder"]["error"][$i]==0){
			$filepath=$tmpdir."/".$name;
			$dir=dirname($filepath);
			if(!is_dir($dir)){
				mkdir($dir,0777,true);
			}
			move_uploaded_file($_FILES["folder"]["tmp_name"][$i],$filepath);
		}
	}

	function addFilesToZip($folder,$zip,$base){
		$files=scandir($folder);
		foreach($files as $file){
			if($file=="."||$file==".."){
				continue;
			}
			$full=$folder."/".$file;
			$local=$base."/".$file;
			if(is_file($full)){
				$zip->addFile($full,$local);
			}elseif(is_dir($full)){
				if(count(scandir($full))>2){
					addFilesToZip($full,$zip,$local);
				}
			}
		}
	}

	$zipname=basename($_FILES["folder"]["name"][0]);
	$pos=strpos($zipname,"/");
	$zipname=$pos!==false?substr($zipname,0,$pos):$zipname;
	$zipfile=$zipname.".zip";

	$zip=new ZipArchive;
	if($zip->open($zipfile,ZipArchive::CREATE)===true){
		addFilesToZip($tmpdir,$zip,"");
		$zip->close();
	}

	function rrmdir($dir){
		foreach(scandir($dir) as $file){
			if($file=="."||$file==".."){
				continue;
			}
			$path=$dir."/".$file;
			if(is_dir($path)){
				rrmdir($path);
			}else{
				unlink($path);
			}
		}
		rmdir($dir);
	}

	rrmdir($tmpdir);

	header("Content-Type: application/zip");
	header("Content-Disposition: attachment; filename=\"$zipfile\"");
	header("Content-Length: ".filesize($zipfile));
	readfile($zipfile);
	unlink($zipfile);
	exit;
?>