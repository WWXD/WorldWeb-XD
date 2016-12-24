<?php

$tables['wiki_pages'] = array
	(
		'fields' => array
		(
			'id' => $var128,
			'revision' => $genericInt,
			'flags' => $genericInt,
		),
		'special' => $keyID
	);
	
$tables['wiki_pages_text'] = array
	(
		'fields' => array
		(
			'id' => $var128,
			'revision' => $genericInt,
			'date' => $genericInt,
			'user' => $genericInt,
			'text' => $text,
		),
		'special' => 'UNIQUE KEY `wpt` (`id`,`revision`)'
	);

