<?php

$tables["uploader"] = array
	(
		"fields" => array
		(
			"id" => $AI,
			"filename" => "varchar(512)".$notNull,
			"description" => $var1024,
			"user" => $genericInt,
			"date" => $genericInt,
			"downloads" => $genericInt,
			"private" => $bool,
			"category" => $genericInt,
		),
		"special" => $keyID
	);

$tables["uploader_categories"] = array
	(
		"fields" => array
		(
			"id" => $AI,
			"name" => $var256,
			"description" => $text,
			"ord" => $genericInt,
		),
		"special" => $keyID
	);
