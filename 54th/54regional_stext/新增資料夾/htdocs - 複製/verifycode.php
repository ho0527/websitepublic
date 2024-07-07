<?php
    include("link.php");
    $c=imagecreate(50,50);
    imagecolorallocate($c,255,255,255);
    imagettftext($c,40,0,15,40,imagecolorallocate($c,0,0,0),"CONSOLAB.TTF",$_GET["str"]);
    imagepng($c);
    header("Content-Type: image/png");
?>