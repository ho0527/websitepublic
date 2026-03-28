<?php
    function saveUploadedFolder($files,$baseDir){
        $names=$files["name"];
        $tmpNames=$files["tmp_name"];
        for($i=0;$i<count($names);$i=$i+1){
            $relativePath=str_replace(["\\",".."],["/",""],$names[$i]); // 防止跳目錄
            $tmpPath=$tmpNames[$i];
            $dest=$baseDir."/".$relativePath;
            $dir=dirname($dest);
            if(!is_dir($dir)){
                mkdir($dir,0777,true);
            }
            move_uploaded_file($tmpPath,$dest);
        }
    }

    function zipFolder($source,$zipFile){
        $zip=new ZipArchive;
        if($zip->open($zipFile,ZipArchive::CREATE|ZipArchive::OVERWRITE)){
            $source=realpath($source);
            $files=new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($source,FilesystemIterator::SKIP_DOTS),
                RecursiveIteratorIterator::LEAVES_ONLY
            );
            foreach($files as $file){
                $filePath=$file->getRealPath();
                $relativePath=substr($filePath,strlen($source)+1);
                $zip->addFile($filePath,$relativePath);
            }
            $zip->close();
        }
    }

    function removeEmptyDirs($path){
        $dirs=new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path,FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach($dirs as $dir){
            if($dir->isDir()&&count(scandir($dir->getPathname()))==2){
                rmdir($dir->getPathname());
            }
        }
    }

    function deleteDir($path){
        $items=array_diff(scandir($path),[".",".."]);
        foreach($items as $item){
            $itemPath=$path."/".$item;
            if(is_dir($itemPath)){
                deleteDir($itemPath);
            }else{
                unlink($itemPath);
            }
        }
        rmdir($path);
    }

    $tmpDir="upload_".uniqid();
    mkdir($tmpDir);

    saveUploadedFolder($_FILES["folder"],$tmpDir);

    $zipFile=$tmpDir.".zip";

    removeEmptyDirs($tmpDir);
    zipFolder($tmpDir,$zipFile);

    header("Content-Type: application/zip");
    header("Content-Disposition: attachment; filename=".basename($zipFile));
    header("Content-Length: ".filesize($zipFile));

    readfile($zipFile);

    deleteDir($tmpDir);
    unlink($zipFile);
    header("location: ./");
?>