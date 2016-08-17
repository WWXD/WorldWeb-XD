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
		"name" => "Per-user data limit (MiB)",
		"default" => 75,
	),
	"uploaderMaxFileSize" => array(
		"type" => "float",
		"name" => "Maximum file size",
		"default" => 20,
	),
);
?>