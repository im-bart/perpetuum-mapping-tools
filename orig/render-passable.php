<?php

set_time_limit(0);

function render_passable($filename, $slope_limit) {
	$fp = fopen($filename, 'rb');

	$im = imagecreatetruecolor(2048, 2048);
	imagesavealpha($im, true);
	imagefill($im, 0, 0, imagecolorallocatealpha($im, 0, 0, 0, 127));

	for ($y = 0; $y < 2048; $y ++) {
		for ($x = 0; $x < 2048; $x ++) {
			// height is a 16 bit integer, length 2 bytes
			// offset = y * 2 bytes, adding x * 2 bytes
			// slopes between vertices x,y; x+1,y; x,y+1; x+1,y+1

			if ($x < 2047 && $y < 2047) {
				$o = ($y * 2048 * 2) + ($x * 2);
				fseek($fp, $o);
				$h1 = current(unpack('s', fread($fp, 2)));
				$h1 = ($h1 / 65535) * 512;

				$o = ($y * 2048 * 2) + (($x + 1) * 2);
				fseek($fp, $o);
				$h2 = current(unpack('s', fread($fp, 2)));
				$h2 = ($h2 / 65535) * 512;

				$o = (($y + 1) * 2048 * 2) + (($x + 1) * 2);
				fseek($fp, $o);
				$h3 = current(unpack('s', fread($fp, 2)));
				$h3 = ($h3 / 65535) * 512;

				$o = (($y + 1) * 2048 * 2) + ($x * 2);
				fseek($fp, $o);
				$h4 = current(unpack('s', fread($fp, 2)));
				$h4 = ($h4 / 65535) * 512;

				$m1 = ($h2 - $h1) / .6; $m1 = atan($m1) * (180 / M_PI); if ($m1 < 0) $m1 = 0 - $m1;
				$m2 = ($h3 - $h2) / .6; $m2 = atan($m2) * (180 / M_PI); if ($m2 < 0) $m2 = 0 - $m2;
				$m3 = ($h4 - $h3) / .6; $m3 = atan($m3) * (180 / M_PI); if ($m3 < 0) $m3 = 0 - $m3;
				$m4 = ($h1 - $h4) / .6; $m4 = atan($m4) * (180 / M_PI); if ($m4 < 0) $m4 = 0 - $m4;

				if ($m1 >= $slope_limit || $m2 >= $slope_limit || $m3 >= $slope_limit || $m4 >= $slope_limit){
					imagesetpixel($im, $x, $y, imagecolorallocate($im, 255, 255, 255));
				}
			}
		}
	}

	fclose($fp);

	imagepng($im, $filename . '.passable.' . $slope_limit . '.png');
	imagedestroy($im);
	flush();
}

for ($i = 0; $i <= 43; $i ++) {
	$filename = 'altitude' . sprintf('%1$04d', $i) . '.bin';
	if (file_exists($filename)) render_passable($filename, 45);
}

for ($i = 0; $i <= 43; $i ++) {
	$filename = 'altitude' . sprintf('%1$04d', $i) . '.bin';
	if (file_exists($filename)) render_passable($filename, 51);
}

for ($i = 0; $i <= 43; $i ++) {
	$filename = 'altitude' . sprintf('%1$04d', $i) . '.bin';
	if (file_exists($filename)) render_passable($filename, 56);
}

exit(0);
