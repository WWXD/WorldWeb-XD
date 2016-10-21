<?php

setlocale(LC_ALL, "nl_NL", "nld_nld");

$birthdayExample = "26 juni, 1983";

$months = array(
	"",
	"Januari",
	"Februari",
	"Maart",
	"April",
	"Mei",
	"Juni",
	"Juli",
	"Augustus",
	"September",
	"Oktober",
	"November",
	"December",
);

$days = array(
	"",
	"Zondag",
	"Maandag",
	"Dinsdag",
	"Woensdag",
	"Donderdag",
	"Vrijdag",
	"Zaterdag",
);

function Plural($i, $s)
{
	if($i == 1)
		return $i." ".$s;

	if(strrpos($s, "post") !== false
	|| strrpos($s, "thread") !== false
	|| strrpos($s, "rankset") !== false)
		$f = "s";
	elseif(strrpos($s, "jaar") !== false)
		$f = "";
	elseif(substr($s, -1) == "y")
	{
		$s = substr($s, 0, -1); //query -> queries
		$f = "ies";
	}
	elseif(substr($s, -2) == "er")
		$f = "s"; //gebruiker -> gebruikers, also picks up on users.
	else
		$f .= "en"; //bericht -> berichten

	return $i." ".$s.$f;
}

function HisHer($user)
{
	if($user['sex'] == 0)
		return "zijn";
	if($user['sex'] == 1)
		return "haar";
	return "diens";
}

function stringtotimestamp($str)
{
	global $months;
	$parts = explode(" ", $str);
	$day = (int)$parts[0];
	$month = $parts[1];
	$month = str_replace(",", "", $month);
	$year = (int)$parts[2];
	for($m = 1; $m <= 12; $m++)
	{
		if(strcasecmp($month, $months[$m]) == 0)
		{
			$month = $m;
			break;
		}
	}
	if((int)$month != $month)
		return 0;
	return mktime(12,0,0, $month, $day, $year);
}

function timestamptostring($t)
{
	if($t == 0)
		return "";
	return strftime("%#d %B, %Y", $t);
}

?>