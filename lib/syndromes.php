<?php
if (!defined('BLARG')) die();

// Don't want syndromes? Just keep this array empty!

$aff = "Affected by ";

$syndromes = array
(

	10 => array("Actually active", "#53C373"),
	20 => array("$aff'Activity Syndrome'", "#69D989"),
	30 => array("$aff'Activity Syndrome' +", "#83F3A3"),

	50 => array("$aff'Mountain Dew Voltage Syndrome'", "#8E83EE"),
	
	100 => array("{$aff}'Polari Syndrome'", '#89ff8d'),
	150 => array("{$aff}'Polari Syndrome' +", '#ffbb35'),
	200 => array("{$aff}'Mega Goomba Syndrome'", '#ff212f'),

	// this one can't be reached unless the minimum interval between posts is 9 or less
	9001 => array("$aff'Goku Syndrome'", "#FF5353"),

/*
	//Here's the set as recorded up to AcmlmBoard 2.0:
	75 => array("$aff'Reinfors Syndrome'", "#83F3A3"),
	100 => array("$aff'Reinfors Syndrome' +", "#FFE323"),
	150 => array("$aff'Reinfors Syndrome' ++", "#FF5353"),
	200 => array("$aff'Reinfors Syndrome' +++", "#CE54CE"),
	250 => array("$aff'Reinfors Syndrome' ++++", "#8E83EE"),
	300 => array("$aff'Wooster Syndrome'!!", "#BBAAFF"),
	350 => array("$aff'Wooster Syndrome' +!!", "#FFB0FF"),
	400 => array("$aff'Wooster Syndrome' ++!!", "#FFB070"),
	450 => array("$aff'Wooster Syndrome' +++!!", "#C8C0B8"),
	500 => array("$aff'Wooster Syndrome' ++++!!", "#A0A0A0"),
	600 => array("$aff'Anya Syndrome'!!!", "#C762F2"),
	800 => array("$aff'Something higher than Anya Syndrome' +++++!!", "#D06030"),

	//And here's the Neritic Net set, as of May 15th 2010:
	25 => array("$aff'Posting Syndrome'", "#53C373"),
	50 => array("$aff'Posting Syndrome' +", "#69D989"),
	75 => array("$aff'Geno Syndrome'", "#83F3A3"),
	100 => array("$aff'Geno Syndrome' +", "#FFE323"),
	150 => array("$aff'Geno Syndrome' ++", "#FF5353"),
	200 => array("$aff'pieguy1372 Syndrome'!", "#CE54CE"),
	250 => array("$aff'pieguy1372 Syndrome' +!", "#8E83EE"),
	300 => array("$aff'pieguy1372 Syndrome' ++!!", "#BBAAFF"),
	350 => array("$aff'Wooster Syndrome' +!!", "#FFB0FF"),
	400 => array("$aff'Wooster Syndrome' ++!!", "#FFB070"),
	450 => array("$aff'Wooster Syndrome' +++!!", "#C8C0B8"),
	500 => array("$aff'Wooster Syndrome' ++++!!", "#A0A0A0"),
	600 => array("$aff'Anya Syndrome'!!!", "#C762F2"),
	800 => array("$aff'Xkeeper Syndrome'!!!!", "#D06030"),
	1000 => array("$aff'Wtf?! Syndrome'!~", "#FF2277"),
*/

);

?>