<?php
	$settings = array(
		"dbserv" => array(
			"type" => "text",
			"default" => "localhost",
			"name" => "MySQL server"
		),
		"dbuser" => array(
			"type" => "text",
			"default" => "",
			"name" => "User name",
		),
		"dbpass" => array(
			"type" => "password",
			"default" => "",
			"name" => "Password",
		),
		"dbname" => array(
			"type" => "text",
			"default" => "",
			"name" => "Database name",
		),
		"maps" => array(
			"type" => "text",
			"default" => "",
			"name" => "Space separated list of maps",
			"help" => "The first map is default.",
		),
		"showlink" => array(
			"type" => "boolean",
			"default" => 0,
			"name" => "Show link",
		),
	);
?>
