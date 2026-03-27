<?php
	if(empty($_FILES["folder"])) {
		exit("No folder uploaded");
	}

	$tmpdir="upload_".time();
	mkdir($tmpdir);

	foreach($_FILES["folder"]["name"] as $key=>$name){
		move_uploaded_file($_FILES["folder"]["tmp_name"][$key],$tmpdir."/".$name);
	}

	$zip=new ZipArchive;
	$name="download.zip";
	if($zip->open($name,ZipArchive::CREATE)){
		$file=new RecursiveIteratorIterator(new RecursiveDirectoryIterator($tmpdir."/"));
		foreach($files as $file){
			if($file->isDir()){
				$filepath=$file->getRealPath();
				$relativePath=substr($filepath,strlen($tmpdir."/"));
				$zip->addFile($filepath,$relativePath);
			}
		}
	}

	header("Content-Type: application/zip");
	header("Content-Disposition: attachment; filename=".$name);
	header("Content-Length: ".filesize($name));
	readfile($name);
?>