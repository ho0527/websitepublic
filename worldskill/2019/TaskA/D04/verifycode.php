<?php
    session_start();
    $str=str_split($_GET["val"]);
    $canva=imagecreate(250,85);//創建畫布
    imagecolorallocate($canva,255,255,255);//創建背景顏色
    $paint=imagecolorallocate($canva,255,0,0);//創建畫筆顏色
    $font=__DIR__."/font.TTF";//TTF檔
    for($i=0;$i<count($str);$i=$i+1){//4碼驗證
        imagettftext($canva,50,rand(-30,30),20+$i*55,65,$paint,$font,$str[$i]);
    }
    for($i=0;$i<3;$i=$i+1){//3條線線
        imageline($canva,rand(0,400),rand(0,100),rand(0,400),rand(0,100),imagecolorallocate($canva,rand(0,255),rand(0,255),rand(0,255)));
    }
    for($i=0;$i<3;$i=$i+1){//3條線線
        imagettftext($canva,10,0,rand(0,400),rand(0,100),imagecolorallocate($canva,rand(0,255),rand(0,255),rand(0,255)),$font,".");
    }
    header("content-Type:image/jpeg");//header宣告為jpeg
    imagejpeg($canva);//創建畫布
    imagedestroy($canva);//清除畫布
?>