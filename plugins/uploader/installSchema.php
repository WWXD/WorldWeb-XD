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
		),
		"special" => $keyID
	);

