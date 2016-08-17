<?php
function rainbowname()
{
	$r = mt_rand(0,359);
	$len = strlen($s);
	$out .= 'hsl('.$r.',100%,80.4%)!important';
	$r += 31;
	$r %= 360;
	return $out;
}