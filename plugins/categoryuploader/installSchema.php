<?php

$tables["uploader"] = array
	(
		"fields" => array
		(
			"id" => 'varchar(16)'.$notNull,
			"filename" => "varchar(512)".$notNull,
			"description" => $var1024,
			'big_description' => $text,
			"user" => $genericInt,
			"date" => $genericInt,
			"downloads" => $genericInt,
			"category" => $genericInt,
			'cattype' => $genericInt,
			'deldate' => $genericInt,
			'physicalname' => 'varchar(64)'.$notNull,
		),
		"special" => $keyID.', key `user` (`user`), key `nyancat` (`cattype`,`category`), key `deldate` (`deldate`)'
	);

$tables["uploader_categories"] = array
	(
		"fields" => array
		(
			"id" => $AI,
			"name" => $var256,
			"description" => $text,
			"ord" => $genericInt,
			'showindownloads' => "tinyint(4) NOT NULL DEFAULT '0'",
			'minpower' => $smallerInt,
		),
		"special" => $keyID
	);
