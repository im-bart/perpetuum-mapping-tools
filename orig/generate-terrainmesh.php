<?php

set_time_limit(0);

$islands = require 'islands.php';

function generate_mesh($source, $target)
{
	$fp = fopen($source, 'rb');
	$fp2 = fopen($target, 'wb');

	$im = imagecreatetruecolor(2048, 2048);
	imagesavealpha($im, true);
	imagefill($im, 0, 0, imagecolorallocatealpha($im, 0, 0, 0, 127));

	for ($y = 0; $y < 2048; $y = $y + 4) {
		for ($x = 0; $x < 2048; $x = $x + 4) {
			// height is a 16 bit integer, length 2 bytes
			// offset = y * 2 bytes, adding x * 2 bytes
			// slopes between vertices x,y; x+1,y; x,y+1; x+1,y+1
			$offset = ($y * 2048 * 2) + ($x * 2);
			fseek($fp, $offset);
			fwrite($fp2, fread($fp, 2));
		}
	}

	fclose($fp);
	fclose($fp2);
}

for ($i = 0; $i <= 43; $i ++) {
	$ident = sprintf('%1$04d', $i);

	if (isset($islands[$ident])) {
		$source = 'altitude' . $ident . '.bin';
		$target = 'webgl/' . $islands[$ident]['id'] . '.bin';
		if (file_exists($source)) {
			generate_mesh($source, $target);
		}
		var_dump($source);
	}
}

exit(0);
