<?php

require(__DIR__.'/config/kurikey.php');

$goom1 = imagecreatefrompng(__DIR__.'/kurichallenge/goomba.png');
$goom2 = imagecreatefrompng(__DIR__.'/kurichallenge/redgoomba.png');
$goom3 = imagecreatefrompng(__DIR__.'/kurichallenge/giantgoomba.png');
$goombas = array($goom1, $goom2, $goom3);


$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
$kuridata = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, md5(KURIKEY, true), base64_decode($_GET['data']), MCRYPT_MODE_ECB, $iv);
if (!$kuridata) die();

$kuridata = explode('|', $kuridata);
if (count($kuridata) != 3) die();
$kuriseed = intval($kuridata[0]);
$check = intval($kuridata[1]);
$kurichallenge = $kuridata[2];
$kurichallenge = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, md5(KURIKEY.$check, true), base64_decode($kurichallenge), MCRYPT_MODE_ECB, $iv);
if (!$kurichallenge) die();

$kurichallenge = explode('|', $kurichallenge);
if (count($kurichallenge) != 3) die();
if ($kurichallenge[0] != $kuridata[0]) die();
if ($kurichallenge[1] != $kuridata[1]) die();

$ngoombas = intval($kurichallenge[2]);


$img = imagecreate(256, 96);
$bg = imagecolorallocate($img, 0, 0, 0);

$tilesoccupied = array();

for ($g = 0; $g < $ngoombas; $g++)
{
	$goom = $goombas[rand(0,count($goombas)-1)];
	$gw = imagesx($goom);
	$gh = imagesy($goom);
	
	for (;;)
	{
		$cx = rand(0, 255-$gw);
		$cy = rand(0, 95-$gh);
		
		if (isset($tilesoccupied[$cy/32][$cx/32]) && $tilesoccupied[$cy/32][$cx/32]) 
			continue;
		$tilesoccupied[$cy/32][$cx/32] = true;
		break;
	}
	
	imagecopy($img, $goom, $cx, $cy, 0, 0, $gw, $gh);
}

header('Content-type: image/png');
imagepng($img);
imagedestroy($img);

foreach ($goombas as $goom)
	imagedestroy($goom);

?>