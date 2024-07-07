<?php
    include("link.php");
    $c=imagecreate(145,50);
    imagecolorallocate($c,255,255,255);
    imagettftext($c,40,0,15,40,imagecolorallocate($c,0,0,0),"CONSOLAB.TTF",$_SESSION["ans"]);
    imagepng($c);
    header("Content-Type: image/png");
?>