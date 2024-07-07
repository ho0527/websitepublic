<?php
    date_default_timezone_set("Asia/Taipei");
    $time=date("YmdHis");
    session_start();

    $foldername=$_POST["foldername"];

    if(preg_match("/\\|\/|\?|\"|\<|\>|\|/",$foldername)){
        $foldername="fuckyou";
    }else{
        if($foldername==""){
            $foldername=$time;
        }
    }

    if(isset($foldername)){
        $j=1;
        while(is_dir($foldername)){
            $foldername=$foldername.$j;
            $j=$j+1;
        }
        mkdir("upload/".$foldername,0777,true);
    }

    if($_FILES["file"]["name"][0]!=""){
        for($i=0;$i<count($_FILES["file"]["name"]);$i=$i+1){
            $file="upload/".$foldername."/".$_FILES["file"]["name"][$i];
            $j=1;
            while(file_exists($file)){
                $file="upload/".$folder."/".$j."_".$_FILES["file"]["name"][$i];
                $j=$j+1;
            }
            move_uploaded_file($_FILES["file"]["tmp_name"][$i],$file);
        }
    }

    if($_FILES["folder"]["name"][0]!=""){
        for($i=0;$i<count($_FILES["folder"]["name"]);$i=$i+1){
            $file="file/".$foldername."/".$_FILES["folder"]["name"][$i];
            $j=1;
            while(file_exists($foldername)){
                $file="upload/".$foldername."/".$j."_".$_FILES["folder"]["name"][$i];
                $j=$j+1;
            }
            mkdir($foldername,0777,true);
            move_uploaded_file($_FILES["folder"]["tmp_name"][$i],$file);
        }
    }

    $response=["success"=>true,"message"=>"Upload success"];
    echo(json_encode($response));
?>