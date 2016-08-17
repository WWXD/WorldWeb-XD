<?php
if (php_sapi_name() !== 'cli')
{
	die("This script is only intended for CLI usage.\n");
}
include("../language.php");

$langnames = array("nl_NL", "es_ES", "pl_PL");
$langs = array();

foreach($langnames as $langname)
{
	$languagePack = array();
	importLanguagePack($langname.".txt");
	$langs[$langname] = $languagePack;
}

foreach($languagePack as $key => $val)
{
	print $key."\t\t";
	foreach($langnames as $langname)
		print $langs[$langname][$key]."\t";
	print "\n";
}
