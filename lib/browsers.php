<?php
if (!defined('BLARG')) die();

$lastKnownBrowser = "blarg";

$knownBrowsers = array
(
	"MSIE" => "Internet Explorer",
	"Opera Tablet" => "Opera Mobile (tablet)",
	"Opera Mobile" => "Opera Mobile",
	"Opera Mini" => "Opera Mini", //Opera/9.80 (J2ME/MIDP; Opera Mini/4.2.18887/764; U; nl) Presto/2.4.15
	'iPod' => 'iPod',
	'iPad' => 'iPad',
	'iPhone' => 'iPhone',
	"Nintendo Wii" => "Wii Internet Channel", //Opera/9.30 (Nintendo Wii; U; ; 3642; nl)
	"Nintendo DSi" => "Nintendo DSi Browser", //Opera/9.50 (Nintendo DSi; Opera/507; U; en-US)
	"Nitro" => "Nintendo DS Browser",
	"Nintendo 3DS" => "Nintendo 3DS",
	"Opera" => "Opera",
	"MozillaDeveloperPreview" => "Firefox (Development build)",
	"Firefox" => "Firefox",
	"dwb" => "DWB",
	"Chrome" => "Chrome",
	"Android" => "Android",
	"Midori" => "Midori",
	"Safari" => "Safari",
	"Konqueror" => "Konqueror",
	"Mozilla" => "Mozilla",
	"Lynx" => "Lynx",
	"ELinks" => "ELinks",
	"Links" => "Links",
	"Nokia" => "Nokia mobile",
);

$mobileBrowsers = array('Opera Tablet', 'Opera Mobile', 'Opera Mini', 'Nintendo DSi', 'Nitro', 'Nintendo 3DS', 'Android', 'Nokia', 'iPod', 'iPad', 'iPhone');
$mobileLayout = false;

$ua = $_SERVER['HTTP_USER_AGENT'];

foreach($knownBrowsers as $code => $name)
{
	if (strpos($ua, $code) !== FALSE)
	{
		$versionStart = strpos($ua, $code) + strlen($code);
		if ($code != "dwb") $version = GetVersion($ua, $versionStart);

		//Opera Mini wasn't detected properly because of the Opera 10 hack.
		if (strpos($ua, "Opera/9.80") !== FALSE && $code != "Opera Mini" || $code == "Safari" && strpos($ua, "Version/") !== FALSE)
			$version = substr($ua, strpos($ua, "Version/") + 8);
			
		if (in_array($code, $mobileBrowsers)) $mobileLayout = true;
		break;
	}
}

if ($_COOKIE['forcelayout'] == 1) $mobileLayout = true;
else if ($_COOKIE['forcelayout'] == -1) $mobileLayout = false;

$oldAndroid = false;
if ($name == 'Android' && $version[0] == '2') $oldAndroid = true;

$lastKnownBrowser = $ua;

function GetVersion($ua, $versionStart)
{
	$numDots = 0;
	$version = "";
	if (strpos($ua, "Linux")) {
		for ($i = ++$versionStart; $i < strlen($ua); $i++) {
			if ($ua[$i] === " ")
				break;
			else if ($ua[$i] != ";") $version .= $ua[$i];
		}
	} else {
		for($i = $versionStart; $i < strlen($ua); $i++)
		{
			$ch = $ua[$i];
			if($ch == ';')
				break;
			if($ch == '_' && strpos($ua, "Mac OS X"))
				$ch = '.';
			if($ch == '.')
			{
				$numDots++;
				if($numDots == 3)
					break;
				$version .= '.';
			}
			else if(strpos("0123456789.-", $ch) !== FALSE)
				$version .= $ch;
		}
	}
	return $version;
}

?>
