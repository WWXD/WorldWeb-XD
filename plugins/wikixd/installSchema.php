<?php

$tables['wiki_pages'] = [
		'fields' => [
			'id' => $var128,
			'revision' => $genericInt,
			'flags' => $genericInt,
		],
		'special' => $keyID
	];
	
$tables['wiki_pages_text'] = [
		'fields' => [
			'id' => $var128,
			'revision' => $genericInt,
			'date' => $genericInt,
			'user' => $genericInt,
			'text' => $text,
		],
		'special' => 'UNIQUE KEY `wpt` (`id`,`revision`)'
	];

