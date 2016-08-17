<?php

if(!function_exists("HandleExtraField"))
{
	function HandleExtraField($field, $item)
	{
		global $pluginSettings;
		$i = $item['fieldnumber'];
		$t = $item['isCaption'] ? "t" : "v";
		$pluginSettings['profileExt'.$i.$t] = urlencode($_POST['extra'.$i.$t]);
		return true;
	}
}

$personal['extrafields'] = array(
	"name" => "Custom profile fields",
	"items" => array(),
);
for($i = 0; $i < Settings::pluginGet('numberOfFields'); $i++)
{
	$personal['extrafields']['items']['extra'.$i.'t'] = array(
		"caption" => format(__("Caption for #{0}"), $i+1),
		"type" => "text",
		"value" => getSetting("profileExt".$i."t", true),
		"callback" => "HandleExtraField",
		"width" => "98%",
		"fieldnumber" => $i,
		"isCaption" => true,
	);
	$personal['extrafields']['items']['extra'.$i.'v'] = array(
		"caption" => format(__("Value for #{0}"), $i+1),
		"type" => "text",
		"value" => getSetting("profileExt".$i."v", true),
		"callback" => "HandleExtraField",
		"width" => "98%",
		"fieldnumber" => $i,
		"isCaption" => false,
	);
}


