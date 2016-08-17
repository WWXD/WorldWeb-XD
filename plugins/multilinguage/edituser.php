<?php
function makeLangList()
{
	$data = array();
	$dir = @opendir("lib/lang");
	while ($file = readdir($dir))
	{
		//print $file;
		if (endsWith($file, "_lang.php"))
		{
			$file = substr($file, 0, strlen($file)-9);
			$data[$file] = $file;
		}
	}
	$data["en_US"] = "en_US";
	$data["-default"] = "Board default";
	closedir($dir);
	ksort($data);
	return $data;
}

$general['presentation']['items']['linguage'] = array(
	"caption" => __("Language"),
	"type" => "radiogroup",
	"options" => makeLangList(),
	"value" => $user['linguage'],
);
