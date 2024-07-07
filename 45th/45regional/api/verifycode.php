<?php
    $canva=imagecreate(50,50);
    imagecolorallocate($canva,255,255,255);
    imagettftext($canva,30,0,15,40,(imagecolorallocate($canva,0,0,0)),(__DIR__."/font.TTF"),$_GET["str"]);
    header("content-type:image/png");
    imagepng($canva);
    imagedestroy($canva);
?>