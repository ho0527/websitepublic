<?php
    $file_name=$_GET["file"];

    function rrmdir($dir){
        if(is_dir($dir)){
            $objects=scandir($dir);
            foreach($objects as $object){
                if($object != "." && $object != ".."){
                    if(is_dir($dir."/".$object) && !is_link($dir."/".$object)){
                        rrmdir($dir."/".$object);
                    } else{
                        unlink($dir."/".$object);
                    }
                }
            }
            rmdir($dir);
        }
    }

    if(isset($_GET["isfolder"])){
        $isfolder=$_GET["isfolder"];
    }else{
        $isfolder=false;
    }

    if($isfolder){
        $dir="upload/".$file_name;
        $files=glob($dir."/*");
        foreach($files as $file){
            if(is_file($file)){
                unlink($file);
            }elseif(is_dir($file)){
                rrmdir($file);
            }
        }
        rmdir($dir);

        echo(json_encode(["success"=>true]));
    }else{
        // 刪除檔案
        $filepath="upload/".$file_name;

        if(file_exists($filepath)){
            unlink($filepath);
            echo(json_encode(["success"=>true]));
        } else{
            echo(json_encode(["success"=>false]));
        }
    }
?>
