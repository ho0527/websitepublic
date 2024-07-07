<?php
    $canva=imagecreate(80,80);
    imagecolorallocate($canva,255,255,255);
    imagettftext($canva,40,0,30,55,imagecolorallocate($canva,0,0,0),__DIR__."/font.TTF",$_GET["str"]);
    header("content-type:image/png");
    imagepng($canva);
?>