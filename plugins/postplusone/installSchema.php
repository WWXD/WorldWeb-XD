<?php

$tables["users"]["fields"]["postplusones"] = $genericInt;
$tables["users"]["fields"]["postplusonesgiven"] = $genericInt;
$tables["posts"]["fields"]["postplusones"] = $genericInt;

$tables["postplusones"] = array
	(
		"fields" => array
		(
			"user" => $genericInt,
			"post" => $genericInt,
		),
		"special" => "primary key (`user`, `post`), key `user` (`user`), key `post` (`post`)"
	);

