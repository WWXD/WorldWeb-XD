<?php

if (php_sapi_name() !== 'cli')
{
	die("This script is only intended for CLI usage.\n");
}

$url = "https://docs.google.com/spreadsheet/pub?key=0Ap4SeFz7d1d9dGFzb2dKWXZtMkN4ZUVVRjZ6eGFhSnc&output=txt";
$handle = @fopen($url, "r");

if (!$handle)
	die("Couldn't open file");

$headerline = fgets($handle);
$headervals = explode("\t", $headerline);

$langcount = count($headervals)-2;
$langs = array();

while (($buffer = fgets($handle, 4096)) !== false)
{
	$vals = explode("\t", $buffer);
	for($i = 0; $i < $langcount; $i++)
		$langs[$i+2][trim($vals[0])] = trim($vals[$i+2]);
}

for($i = 0; $i < $langcount; $i++)
{
	$lang = $langs[$i+2];
	$langname = $headervals[$i+2];

	$outhandle = fopen($langname."_lang.php", "w");

	fwrite($outhandle, '<?php
	// This file has been automatically generated from the translation spreadsheet.
	// DONT EDIT IT.
	$languagePack = array(
');

	foreach($lang as $a => $b)
		fwrite($outhandle, "'".str_replace("'", "\\'", $a)."' => '".str_replace("'", "\\'", $b)."',\n");

	fwrite($outhandle, ');');
}

fclose($handle);

print "Done!\n";
