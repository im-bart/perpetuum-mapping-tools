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
	
	function generate_mesh($source, $target)
	{
		$fp = fopen($source, 'rb');
		$fp2 = fopen($target, 'wb');
		
		$im = imagecreatetruecolor(2048, 2048);
		imagesavealpha($im, true);
		imagefill($im, 0, 0, imagecolorallocatealpha($im, 0, 0, 0, 127));
		
		for ($y = 0; $y < 2048; $y = $y + 4)
		{
			for ($x = 0; $x < 2048; $x = $x + 4)
			{
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
	
	exit();