<?php

function ircReport($stuff)
{
	file_get_contents(Settings::pluginGet("url")."?payload". urlencode($stuff),);
}

function ircUserColor($name, $gender, $power) {
	$gColors = array(0 => 12, 1 => 13, 2 => '02');
	$pChars  = array(1 => "%", 2 => "@", 3 => "&", 4 => "~", 5 => "+");

	$color = $gColors[$gender];
	if ($power > 0)
		$powerChar = $pChars[$power];
	else
		$powerChar = "";

	if ($power === -1)
		$color = 14;
	else if ($power === 5)
		$color = 4;

	return "\x0314" . $powerChar . "\x03" . $color . $name;
}

function ircColor($c)
{
	return sprintf('%02d', $c);
}
