<?php
    $str=$_GET["str"]; // 拿到資料(文字)
    $canva=imagecreate(50,50); // 創建畫布
    imagecolorallocate($canva,255,255,255); // 背景
    $paint=imagecolorallocate($canva,0,0,0); // 筆的顏色
    $font="font.TTF"; // 字型
    imagettftext($canva,30,0,15,40,$paint,$font,$str); // 設定 大小,旋轉角,x偏移,y偏移,畫筆,字型,文字
    header("content-type:image/png"); // 設定content-type(可加可不加)
    imagepng($canva); // 設定成png
    imagedestroy($canva); // 清除(可加可不加)
?>