<?php
	$cell=isset($_GET["cell_size"])?intval($_GET["cell_size"]):50;
	if($cell<=0){
		$cell=50;
	}

	$src=imagecreatefromjpeg("original.jpg");
	$width=imagesx($src);
	$height=imagesy($src);

	$mosaic=imagecreatetruecolor($width,$height);

	for($y=0;$y<$height;$y=$y+$cell){
		for($x=0;$x<$width;$x=$x+$cell){
			$r=0;
			$g=0;
			$b=0;
			$count=0;
			for($dy=0;$dy<$cell;$dy=$dy+1){
				for($dx=0;$dx<$cell;$dx=$dx+1){
					$px=$x+$dx;
					$py=$y+$dy;
					if($px<$width&&$py<$height){
						$rgb=imagecolorat($src,$px,$py);
						$r=$r+(($rgb>>16)&255);
						$g=$g+(($rgb>>8)&255);
						$b=$b+($rgb&255);
						$count=$count+1;
					}
				}
			}
			if($count>0){
				$r=intval($r/$count);
				$g=intval($g/$count);
				$b=intval($b/$count);
				$color=imagecolorallocate($mosaic,$r,$g,$b);
				imagefilledrectangle($mosaic,$x,$y,$x+$cell-1,$y+$cell-1,$color);
			}
		}
	}

	header("Content-Type: image/jpeg");
	imagejpeg($mosaic);
	imagedestroy($src);
	imagedestroy($mosaic);
?>