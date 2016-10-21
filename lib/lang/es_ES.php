<?php

setlocale(LC_ALL, "es_ES");

$birthdayExample = "Junio 26, 1983";

$dateformats = array("", "m-d-y", "d-m-y", "y-m-d", "Y-m-d", "m/d/Y", "d.m.y", "M j Y", "D jS M Y");
$timeformats = array("", "h:i A", "h:i:s A", "H:i", "H:i:s");

$months = array(
	"",
	"Enero",
	"Febrero",
	"Marzo",
	"Abril",
	"Mayo",
	"Junio",
	"Julio",
	"Agosto",
	"Septiembre",
	"Octubre",
	"Noviembre",
	"Diciembre",
);

$days = array(
	"",
	"Domingo",
	"Lunes",
	"Martes",
	"Miercoles",
	"Jueves",
	"Viernes",
	"Sabado",
);

function PluralWord($s)
{
	if($s == "MySQL")
		return $s;

	return $s."s";
}

function Plural($i, $s)
{
	if($i == 1) //For 1, just return that.
		return $i." ".$s;

	$s = explode(" ", $s);
	$s = array_map("PluralWord", $s);
	$s = implode(" ", $s);

	return $i." ".$s;
}

function HisHer($user)
{
	//Heh, this doesn't take plurals into account...
	return "su";
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
