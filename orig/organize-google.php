<?php

set_time_limit(0);

$islands = array(
	'0000' => array(
		'id' => 1,
		'name' => 'New Virginia'
	),
	'0001' => array(
		'id' => 2,
		'name' => 'Attalica'
	),
	'0002' => array(
		'id' => 3,
		'name' => 'Daoden'
	),
	'0003' => array(
		'id' => 8,
		'name' => 'Domhalarn'
	),
	'0004' => array(
		'id' => 9,
		'name' => 'Hokkogaros'
	),
	'0005' => array(
		'id' => 7,
		'name' => 'Norhoop'
	),
	'0006' => array(
		'id' => 5,
		'name' => 'Tellesis'
	),
	'0007' => array(
		'id' => 6,
		'name' => 'Shinjalar'
	),
	'0008' => array(
		'id' => 4,
		'name' => 'Hershfield'
	),
	'0009' => array(
		'id' => 12,
		'name' => 'Kentagura'
	),
	'0010' => array(
		'id' => 10,
		'name' => 'Alsbale'
	),
	'0011' => array(
		'id' => 11,
		'name' => 'Novastrov'
	),
	'0020' => array(
		'id' => 29,
		'name' => 'Davis Barrier'
	),
	'0021' => array(
		'id' => 30,
		'name' => 'Gravehills'
	),
	'0022' => array(
		'id' => 31,
		'name' => 'Emperth'
	),
	'0023' => array(
		'id' => 32,
		'name' => 'Guthraw'
	),
	'0024' => array(
		'id' => 33,
		'name' => 'Landers Bridge'
	),
	'0025' => array(
		'id' => 34,
		'name' => 'Solarfield'
	),
	'0026' => array(
		'id' => 35,
		'name' => 'Blackpoint'
	),
	'0027' => array(
		'id' => 36,
		'name' => 'Greensward'
	),
	'0028' => array(
		'id' => 21,
		'name' => 'Novaya Trava'
	),
	'0029' => array(
		'id' => 22,
		'name' => 'Neuhorn'
	),
	'0030' => array(
		'id' => 23,
		'name' => 'Kraslovsk'
	),
	'0031' => array(
		'id' => 24,
		'name' => 'Langruhm'
	),
	'0032' => array(
		'id' => 25,
		'name' => 'Berger\'s Island'
	),
	'0033' => array(
		'id' => 26,
		'name' => 'Clandrais'
	),
	'0034' => array(
		'id' => 27,
		'name' => 'Bleumon'
	),
	'0035' => array(
		'id' => 28,
		'name' => 'Chalydor'
	),
	'0036' => array(
		'id' => 13,
		'name' => 'Rhaoshan'
	),
	'0037' => array(
		'id' => 14,
		'name' => 'Changowa'
	),
	'0038' => array(
		'id' => 15,
		'name' => 'Mhenosha'
	),
	'0039' => array(
		'id' => 16,
		'name' => 'Xiantior'
	),
	'0040' => array(
		'id' => 17,
		'name' => 'Nirayon'
	),
	'0041' => array(
		'id' => 18,
		'name' => 'Shuzhon'
	),
	'0042' => array(
		'id' => 19,
		'name' => 'Imidero'
	),
	'0043' => array(
		'id' => 20,
		'name' => 'Yuraion Ro'
	)
);

$zoomMap = 18;   // Google maps zoom level of source image

foreach($islands as $ident => $island)
{
	$filenames = array(
		//'map'    => 'killboard/' . $islands[$ident]['id'] . '.png',
		//'pass45' => 'altitude' . $ident . '.bin.passable.45.png',
		//'pass51' => 'altitude' . $ident . '.bin.passable.51.png',
		//'pass56' => 'altitude' . $ident . '.bin.passable.56.png'
	);

	$name = $islands[$ident]['name'];
	$name = strtolower($name);
	$name = str_replace(' ', '-', $name);
	$name = str_replace('\'', '', $name);

	if (!file_exists('google/' . $name)) {
		mkdir('google/' . $name, 0666);
	}

	foreach($filenames as $folder => $filename)
	{
		if (!file_exists('google/' . $name . '/' . $folder)) {
			mkdir('google/' . $name . '/' . $folder, 0666);
		}

		$map = imagecreatefrompng($filename);
		if ($folder == 'pass45' || $folder == 'pass51' || $folder == 'pass56') {
			imagefilter($map, IMG_FILTER_COLORIZE, -(255 - 190), -(255 - 144), -(255 - 22));
			imageopacity($map, 70);
		}

		for ($zoom = $zoomMap; $zoom >= 0; $zoom --)
		{
			$factor = pow(0.5, ($zoomMap - $zoom));
			$bound = pow(2, $zoom) / 2;
			$tilesRequired = ceil((2048 * $factor) / 256);

			$canvas = imagecreatetruecolor($tilesRequired * 256, $tilesRequired * 256);
			imagesavealpha($canvas, true);
			imagefill($canvas, 0, 0, imagecolorallocatealpha($canvas, 0, 0, 0, 127));
			imagecopyresampled($canvas, $map, 0, 0, 0, 0, 2048 * $factor, 2048 * $factor, 2048, 2048);

			for ($x = 0; $x < $tilesRequired; $x ++)
			{
				for ($y = 0; $y < $tilesRequired; $y ++)
				{
					$tile = imagecreatetruecolor(256, 256);
					imagesavealpha($tile, true);
					imagefill($tile, 0, 0, imagecolorallocatealpha($tile, 0, 0, 0, 127));
					imagecopy($tile, $canvas, 0, 0, $x  * 256, $y * 256, 256, 256);

					$tilefile = 'google/' . $name . '/' . $folder . '/' . $zoom . '.' . ($x + $bound) . '.' . ($y + $bound) . '.png';
					imagepng($tile, $tilefile);
					imagedestroy($tile);
				}
			}

			imagedestroy($canvas);
		}
	}

	imagedestroy($map);
	flush();
}

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
