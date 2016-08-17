<?php
//Daily Flat theme

$css = file_get_contents("../flatgray/style.css");

header("Content-Type: text/css");

$curtime = getdate(time());
$min = $curtime['hours'] * 60 + $curtime['minutes'] + 340;

$hue = ($min / 2) % 360;
$sat = 50;
$hs = $hue.", ".$sat."%";

$css = str_replace("(0, 0%", "(".$hs, $css);
print $css;
