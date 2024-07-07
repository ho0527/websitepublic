<?php
    $c=imagecreate(50,50);
    imagecolorallocate($c,12,12,15);
    imagettftext($c,30,0,15,40,imagecolorallocate($c,255,255,255),"CONSOLAB.TTF",$_GET["str"]);
    imagepng($c);
?>