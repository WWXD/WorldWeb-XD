<?php

setlocale(LC_ALL, "da_DK");

$birthdayExample = "26. juni, 1983";

$dateformats = array("", "m-d-y", "d-m-y", "y-m-d", "Y-m-d", "m/d/Y", "d.m.y", "M j Y", "D jS M Y");
$timeformats = array("", "h:i A", "h:i:s A", "H:i", "H:i:s");

$months = array(
	"",
	"Januar",
	"Februar",
	"Marts",
	"April",
	"Maj",
	"Juni",
	"Juli",
	"August",
	"September",
	"Oktober",
	"November",
	"December",
);

$days = array(
	"",
	"Søndag",
	"Mandag",
	"Tirsdag",
	"Onsdag",
	"Torsdag",
	"Fredag",
	"Lørdag",
);

function Plural($i, $s)
{
	$e = explode(" ", $a);

	if($i == 1) //For 1, just return that.
		return $i." ".$s;
	if ($s2[count($s)-1] == "indlæg")
		$s2[count($s)-2] .= "e";
	else if ($s2[count($s)-1] == "tråd")
		$s2[count($s)-1] .= "e";
	else
		$s2[count($s)-1] .= "er"; //record -> records

	return $i." ".implode(" ", $s2);
}

function HisHer($user)
{
	return "sin";
}

function stringtotimestamp($str)
{
	global $months;
	$parts = explode(" ", $str);
	$day = (int)$parts[1];
	$month = $parts[0];
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
	return strftime("%B %#d, %Y", $t);
}

?>
