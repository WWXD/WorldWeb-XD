<?php

function ircReport($stuff)
{
	$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	socket_connect($sock, Settings::pluginGet("host"), Settings::pluginGet("port"));
	socket_write($sock, $stuff."\n");
	socket_close($sock);
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
