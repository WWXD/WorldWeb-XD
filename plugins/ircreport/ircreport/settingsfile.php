<?php
	$settings = array(
		"host" => array(
			"type" => "text",
			"default" => "localhost",
			"name" => "Destination host",
		),
		"port" => array(
			"type" => "integer",
			"default" => "1234",
			"name" => "Destination port",
		),
		"color1" => array(
			"type" => "integer",
			"default" => "5",
			"name" => "Color code for highlights",
		),
		"color2" => array(
			"type" => "integer",
			"default" => "3",
			"name" => "Color code",
		),
		"reportPassMatches" => array(
			"type" => "boolean",
			"default" => "0",
			"name" => "Report number of password matches",
		),
		"reportIPMatches" => array(
			"type" => "boolean",
			"default" => "0",
			"name" => "Report number of IP matches",
		),
	);
?>
