<?php
if (!defined('BLARG')) die();

// TODO make this work in templates at all
// we'll consider it whenever there's enough demand.

//define("PHASE", 2);

$language = Settings::get("defaultLanguage");

include_once(__DIR__."/lang/".$language.".php");
if($language != "en_US")
	include_once(__DIR__."/lang/".$language."_lang.php");

function __($english, $flags = 0)
{
	global $languagePack, $language;
	if($language != "en_US")
	{
		if(!isset($languagePack))
		{
			if(is_file(__DIR__."/lang/".$language.".txt"))
			{
				importLanguagePack(__DIR__."/lang/".$language.".txt");
				importPluginLanguagePacks($language.".txt");
			}
			else
				$final = $english;
		}
		if(!isset($languagePack))
			$languagePack = array();
		$eDec = html_entity_decode($english, ENT_COMPAT, "UTF-8");
		if(array_key_exists($eDec, $languagePack))
			$final = $languagePack[$eDec];
		elseif(array_key_exists($english, $languagePack))
			$final = $languagePack[$english];
		if($final == "")
			$final = $english; //$final = "[".$english."]";
	}
	else
		$final = $english;

	if($flags & 1)
		return str_replace(" ", "&nbsp;", htmlspecialchars($final));
	else if($flags & 2)
		return html_entity_decode($final);
	return $final	;
}

function importLanguagePack($file)
{
	global $languagePack;
	$f = file_get_contents($file);
	$f = explode("\n", $f);
	for($i = 0; $i < count($f); $i++)
	{
		$k = trim($f[$i]);
		if($k == "" || $k[0] == "#")
			continue;
		$i++;
		$v = trim($f[$i]);
		if($v == "")
			continue;
		$languagePack[$k] = $v;
	}
}

function importPluginLanguagePacks($file)
{
	$pluginsDir = @opendir("plugins");
	if($pluginsDir !== FALSE)
	while(($plugin = readdir($pluginsDir)) !== FALSE)
	{
		if($plugin == "." || $plugin == "..") continue;
		if(is_dir("./plugins/".$plugin))
		{
			$foo = "./plugins/".$plugin."/".$file;
			if(file_exists($foo))
				importLanguagePack($foo);
		}
	}
}

?>
