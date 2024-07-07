<?php
    $dir=$_GET["folder"];

    if(is_dir($dir)){
        $file=scandir($dir);
        $filelist=[];

        for($i=0;$i<count($file);$i=$i+1){
            if($file[$i]!="."&&$file[$i]!=".."){
                $path=$dir."/".$file[$i];
                if(is_dir($path)){
                    $filelist[]=["name"=>$file[$i],"isfolder"=>true];
                }else{
                    $filelist[]=["name"=>$file[$i],"isfolder"=>false];
                }
            }
        }

        echo(json_encode(["success"=>true,"filelist"=>$filelist]));
    }else{
        echo(json_encode(["success"=>false]));
    }
?>
