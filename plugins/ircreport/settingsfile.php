<?php
	$settings = array(
		"mode" => array(
			"type" => "options",
			"options" => array('tcp' => 'tcp', 'http' => 'http'),
			"default" => 'tcp',
			"name" => "Connection type",
		),
		"host" => array(
			"type" => "text",
			"default" => "localhost",
			"name" => "(TCP) Destination host",
		),
		"port" => array(
			"type" => "integer",
			"default" => "1234",
			"name" => "(TCP) Destination port",
		),
		"url" => array(
			"type" => "text",
			"default" => "",
			"name" => "(HTTP) Destination URL prefix",
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
