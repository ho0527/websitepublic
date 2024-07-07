<?php
    $file=glob("test/"."*.{png,jpg}",GLOB_BRACE);
    for($i=0;$i<count($file);$i=$i+1){
        $image=imagecreatefromstring(file_get_contents($file[$i]));
        // 設定文字大小
        $bbox=imagettfbbox(50,0,"font.ttf","WorldSkills");
        // 設定文字位置
        $x=imagesx($image)-$bbox[2]-10;
        $y=imagesy($image)-$bbox[1]-10;
        // 加上水平的 "WorldSkills" 文字
        imagettftext($image,50,0,$x,$y,imagecolorallocate($image,255,255,255),"font.ttf","WorldSkills");
        if(!file_exists("output/")){ mkdir("output/",0777,true); } // 確認輸出資料夾存在
        imagepng($image,"output/".basename($file[$i]));
    }
?>