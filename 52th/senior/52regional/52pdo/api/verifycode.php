<?php
    $canva=imagecreate(60,60);
    imagecolorallocate($canva,255,255,255);
    imagettftext($canva,35,0,20,45,(imagecolorallocate($canva,0,0,0)),(__DIR__."/font.TTF"),$_GET["val"]);
    header("content-type:image/png");
    imagepng($canva);
    imagedestroy($canva);
?>