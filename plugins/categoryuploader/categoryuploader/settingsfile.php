<?php
$settings = array(
	"uploaderWhitelist" => array(
		"type" => "text",
		"name" => "Uploader whitelist",
		// Took from ABXD 2.2.4
		"default" => 'png gif jpg jpeg txt css zip rar 7z ogg mp3 swf ogv mp4 webm',
	),
	"uploaderCap" => array(
		"type" => "float",
		"name" => "Uploader size cap (MiB)",
		"default" => 32,
	),
	"personalCap" => array(
		"type" => "float",
		"name" => "Uploader private cap",
		"default" => 0.25,
	),
	"uploaderMaxFileSize" => array(
		"type" => "float",
		"name" => "Uploader max file size",
		"default" => 2,
	),
);
?>