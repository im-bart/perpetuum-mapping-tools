<?php

set_time_limit(0);

function render_blocks($filename) {
	$heights = array();
	
	$fp = fopen($filename, 'rb');
	
	$im = imagecreatetruecolor(2048, 2048);
	imagesavealpha($im, true);
	imagefill($im, 0, 0, imagecolorallocatealpha($im, 0, 0, 0, 127));
			
	$x = 1255; $y = 727;
	$o = ($y * 2048 * 2) + ($x * 2);
	fseek($fp, $o);
	$h = current(unpack('s', fread($fp, 2)));
	
	for ($y = 0; $y < 2048; $y ++) {
		for ($x = 0; $x < 2048; $x ++) {
			// offset = y * 2 bytes, adding x * 2 bytes
			
			$o = ($y * 2048 * 2) + ($x * 2);
			fseek($fp, $o);
			$h = current(unpack('s', fread($fp, 2)));
			
			$heights[$h] = true;
			
			if (
				$h != 0 &&
				$h != 8 && // water
				$h != 770 &&
				$h != 1026 &&
				$h != 1282 &&
				$h != 1538 &&
				$h != 1794 &&
				$h != 2050 &&
				$h != 2306 &&
				$h != 4098 &&
				$h != 4610 &&
				$h != 4866 &&
				$h != 5122 &&
				$h != 6402 && // rare dot
				$h != 8194 &&
				$h != 9474 &&
				$h != 12290 && // rare dot
				$h != 13570
			) {
				imagesetpixel($im, $x, $y, imagecolorallocate($im, 255, 255, 255));
			}	
		}
		
	}
	
	fclose($fp);
	
	//echo '<pre>';
	//print_r($heights);
	//exit;
	
	ob_end_clean();
	header('Content-Type: image/png');
	// imagepng($im);
	imagepng($im, $filename . '.png');
	imagedestroy($im);
	
	exit();
}

for ($i = 0; $i <= 43; $i ++) {
	$filename = 'blocks' . sprintf('%1$04d', $i) . '.bin';
	if (file_exists($filename)) render_blocks($filename);
}

exit();