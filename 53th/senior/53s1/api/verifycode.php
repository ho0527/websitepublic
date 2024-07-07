<?php
    $c=imagecreate(50,50);
    imagecolorallocate($c,255,255,255);
    imagettftext($c,30,0,15,35,imagecolorallocate($c,0,0,0),"font.TTF",$_GET["str"]);
    imagepng($c);
    header("Content-Type:image/png");
?>