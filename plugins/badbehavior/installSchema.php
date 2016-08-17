<?php

$tables['bad_behavior'] = array
	(
		'fields' => array
		(
			'id' => $AI,
			'ip' => $var128,
			'date'=> "DATETIME NOT NULL default '0000-00-00 00:00:00'",
			'request_method' => $text,
			'request_uri' => $text,
			'server_protocol' => $text,
			'http_headers' => $text,
			'user_agent' => $text,
			'request_entity' => $text,
			'key' => $text,
		),
		'special' => "INDEX (`ip`(15)), INDEX (`user_agent`(10)), $keyID",
	);
