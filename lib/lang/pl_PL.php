<?php

setlocale(LC_ALL, "pl_PL");

$birthdayExample = "26 Lipca, 1983";

$dateformats = array("", "m-d-y", "d-m-y", "y-m-d", "Y-m-d", "m/d/Y", "d.m.y", "M j Y", "D jS M Y");
$timeformats = array("", "h:i A", "h:i:s A", "H:i", "H:i:s");

// yay for mega insane months array :P
$months = array(
	'',
	'Styczeń',
	'Luty',
	'Marzec',
	'Kwiecień',
	'Maj',
	'Czerwiec',
	'Lipiec',
	'Sierpień',
	'Wrzesień',
	'Październik',
	'Listopad',
	'Grudzień',

	'Stycznia',
	'Lutego',
	'Marca',
	'Kwietnia',
	'Maja',
	'Czerwca',
	'Lipca',
	'Sierpnia',
	'Września',
	'Października',
	'Listopada',
	'Grudnia',
	
	// and for English - because of ABXD bug in editprofile.php
	
	'January',
	'February',
	'March',
	'April',
	'May',
	'June',
	'July',
	'August',
	'September',
	'October',
	'November',
	'December',
);

$days = array(
	"",
	"Niedziela",
	"Poniedziałek",
	"Wtorek",
	"Środa",
	"Czwartek",
	"Piątek",
	"Sobota",
);


function Plural($i, $s)
{
$wordto2=array(
	'użytkownik'=>'użytkowników',
	'godziny'=>'godzin',
	'użytkownika'=>'użytkowników',
	'prywatną wiadomość'=>'prywatne wiadomości',
	'post'=>'posty',
	'temat'=>'tematy',
	'plik'=>'pliki',
	'MySQL query'=>'MySQL queries',
	// hardcoding filename, because to plural form already with link goes - making things
	// hard with not English based language
	'nową <a href="' . actionLink("private") . '">prywatną wiadomość'=>'nowe <a href="' . actionLink("private") . '">prywatne wiadomości',
	'gość'=>'gości',
	'bot'=>'boty',
	'nowy post'=>'nowe posty',
	'year'=>'years',
	'zapytanie MySQL'=>'zapytania MySQL',
);
$wordto5=array(
	'użytkownik'=>'użytkowników',
	'godziny'=>'godzin',
	'użytkownika'=>'użytkowników',
	'post'=>'postów',
	'temat'=>'tematów',
	'prywatną wiadomość'=>'prywatnych wiadomości',
	'plik'=>'plików',
	'MySQL query'=>'MySQL queries',
	'nową <a href="private.php">prywatną wiadomość'=>'nowych <a href="private.php">prywatnych wiadomości',
	'gość'=>'gości',
	'bot'=>'botów',
	'nowy post'=>'nowe postów',
	'year'=>'years',
	'zapytanie MySQL'=>'zapytań MySQL',
);
	if($i>1&&$i<5){ // from two to four
		if(isset($wordto2[$s]))
			$s=$wordto2[$s];
	}
	elseif($i>4||$i<1) // 0 and 5+ makes other form
		if(isset($wordto5[$s]))
			$s=$wordto5[$s];
	return $i." ".$s;
}

function HisHer($user)
{
	// Why I bother with it, as software doesn't use this function EVEN once...
	if($user['sex'] == 1)
		return "jej";
	return "jego"; // yes - Polish doesn't have "its" form (this is "her")
				   // (you can try his dog and its dog on Google Translate)
				   // (it's always on safer site to consider unknown gender to be male)
}

function stringtotimestamp($str)
{
	global $months;
	$parts = explode(" ", $str);
	$day = (int)$parts[0];
	$month = $parts[1];
	$month = str_replace(",", "", $month);
	$year = (int)$parts[2];
	for($m = 0; $m <= 35; $m++)
	{
		if(strcasecmp($month, $months[$m]) == 0)
		{
			$m%=12;
			$month = $m;
			break;
		}
	}
	$month=(int)$month;
	if((int)$month != $month)
		return 0;
	if((mktime(12,0,0, $month, $day, $year))!==((int)(mktime(12,0,0, $month, $day, $year))))
	$value=0; else $value=mktime(12,0,0, $month, $day, $year);
	return $value;
}

function timestamptostring($t)
{
	if($t == 0)
		return "";
	return strftime("%#d %B, %Y", $t);
}

?>