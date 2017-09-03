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

			if (!in_array($h, [
				0, 770, 1026, 1282, 1538, 1794, 2050, 2306, 4098, 4610, 4866, 5122, 8194, 9474, 13570,
				8, // water
				6402, 12290, // rare dot
			])) {
				imagesetpixel($im, $x, $y, imagecolorallocate($im, 255, 255, 255));
			}
		}
	}

	fclose($fp);

	ob_end_clean();
	header('Content-Type: image/png');
	imagepng($im, $filename . '.png');
	imagedestroy($im);

	exit(0);
}

for ($i = 0; $i <= 43; $i ++) {
	$filename = 'blocks' . sprintf('%1$04d', $i) . '.bin';
	if (file_exists($filename)) render_blocks($filename);
}

exit(0);
