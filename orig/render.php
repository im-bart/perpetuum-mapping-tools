<?php

set_time_limit(0);

function render($filename) {
	$fp = fopen($filename, 'rb');

	$im_color = imagecreatetruecolor(2048, 2048);
	$im_greyscale = imagecreatetruecolor(2048, 2048);
	$nm = imagecreatetruecolor(2048, 2048);
	$ag = imagecreatefrompng('altitudeGradientShaded.png');

	for ($y = 0; $y < 2048; $y ++) {
		for ($x = 0; $x < 2048; $x ++) {
			$value = fread($fp, 2);
			$value = unpack('s', $value);
			$value = current($value);

			if ($value < 55) $value = 0; // water
			$height = $value * (511 / (65535 / 2)); // remap because of max height
			if ($height > 511) $height = 511;

			if ($height > 255) {
				$r = 255;
				$g = $height - 255;
			} else {
				$r = $height;
				$g = 0;
			}

			imagesetpixel($im_greyscale, $x, $y, imagecolorallocate($im_greyscale, $r, $g, 0));

			$rgb = imagecolorat($ag, $height, 128);
			$r = ($rgb >> 16) & 0xFF;
			$g = ($rgb >> 8) & 0xFF;
			$b = $rgb & 0xFF;

			imagesetpixel($im_color, $x, $y, imagecolorallocate($im_color, $r, $g, $b));
		}
	}

	fclose($fp);

	for ($y = 0; $y < 2048; $y ++) {
		for ($x = 0; $x < 2048; $x ++) {
			$rgb = imagecolorat($im_greyscale, $x, $y);
			$r = ($rgb >> 16) & 0xFF;
			$g = ($rgb >> 8) & 0xFF;
			$b = $rgb & 0xFF;
			$height = $r + $g + $b;
			$current = $r;

			$next = $current;
			if ($x < 2047) {
				$rgb = imagecolorat($im_greyscale, $x + 1, $y);
				$r = ($rgb >> 16) & 0xFF;
				$g = ($rgb >> 8) & 0xFF;
				$b = $rgb & 0xFF;
				$next = $r;
			}

			$shade = 128;
			if ($current < $next) {
				$shade = 128 + (($next - $current) * 8); // light
			}
			if ($current > $next) {
				$shade = 128 - (($current - $next) * 8); // shadow
			}

			if ($shade > 255) {
				$shade = 255;
			}
			if ($shade < 0) {
				$shade = 0;
			}

			$rgb = imagecolorat($ag, $height, 255 - $shade);
			$r = ($rgb >> 16) & 0xFF;
			$g = ($rgb >> 8) & 0xFF;
			$b = $rgb & 0xFF;

			imagesetpixel($im_color, $x, $y, imagecolorallocate($im_color, $r, $g, $b));
		}
	}


	header('Content-Type: image/png');
	imagepng($im_color);
	exit(0);

	imagepng($im_color, $filename . '.png');
	imagedestroy($im_color);
	imagedestroy($im_greyscale);
	imagedestroy($nm);
	imagedestroy($ag);
}

for ($i = 4; $i <= 43; $i ++) {
	$filename = 'altitude' . sprintf('%1$04d', $i) . '.bin';
	if (file_exists($filename)) render($filename);
}

exit(0);
