<?php

set_time_limit(0);

$islands = require 'islands.php';

foreach($islands as $ident => $island)
{
	$terrainPng = 'altitude' . $ident . '.bin.png';
	$structuresPng = 'blocks' . $ident . '.bin.png';
	if (!file_exists($terrainPng)) {
		return;
	}
	$terrainGd = imagecreatefrompng($terrainPng);
	$structuresGd = imagecreatefrompng($structuresPng);
	$structuresShadowGd = imagecreatefrompng($structuresPng);
	imagesavealpha($structuresShadowGd, true);

	$targetGd = imagecreatetruecolor(2048, 2048);
	imagesavealpha($targetGd, true);
	imagecopy($targetGd, $terrainGd, 0, 0, 0, 0, 2048, 2048);

	imagefilter($structuresShadowGd, IMG_FILTER_COLORIZE, -255, -255, -255);
	imageopacity($structuresShadowGd, 50);
	imagecopy($targetGd, $structuresShadowGd, 1, 1, 0, 0, 2048, 2048);
	imagefilter($structuresGd, IMG_FILTER_COLORIZE, -(255 - 190), -(255 - 144), -(255 - 22));
	imagecopy($targetGd, $structuresGd, 0, 0, 0, 0, 2048, 2048);

	imagepng($targetGd, 'killboard/' . $island['id'] . '.png');

	imagedestroy($terrainGd);
	imagedestroy($structuresGd);
	imagedestroy($structuresShadowGd);
	imagedestroy($targetGd);
}

exit(0);

//params: image resource id, opacity in percentage (eg. 80)
function imageopacity(&$img, $opacity) {
	if(!isset($opacity)) {
		return false;
	}
	$opacity /= 100;

	//get image width and height
	$w = imagesx( $img );
	$h = imagesy( $img );

	//turn alpha blending off
	imagealphablending( $img, false );

	//find the most opaque pixel in the image (the one with the smallest alpha value)
	$minalpha = 127;
	for ($x = 0; $x < $w; $x++) {
		for ($y = 0; $y < $h; $y++) {
			$alpha = (imagecolorat($img, $x, $y) >> 24) & 0xFF;
			if ($alpha < $minalpha) {
				$minalpha = $alpha;
			}
		}
	}

	//loop through image pixels and modify alpha for each
	for ($x = 0; $x < $w; $x++) {
		for ( $y = 0; $y < $h; $y++ ) {
			//get current alpha value (represents the TRANSPARENCY!)
			$colorxy = imagecolorat( $img, $x, $y );
			$alpha = ( $colorxy >> 24 ) & 0xFF;

			//calculate new alpha
			if ($minalpha !== 127) {
				$alpha = 127 + 127 * $opacity * ($alpha - 127) / (127 - $minalpha);
			} else {
				$alpha += 127 * $opacity;
			}

			//get the color index with new alpha
			$alphacolorxy = imagecolorallocatealpha($img, ($colorxy >> 16) & 0xFF, ($colorxy >> 8) & 0xFF, $colorxy & 0xFF, $alpha);

			//set pixel with the new color + opacity
			if (!imagesetpixel($img, $x, $y, $alphacolorxy)) {
				return false;
			}
	}

	return true;
}
