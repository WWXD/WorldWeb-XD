<?php
// Plugin by Kawa for ABXD, edited by LifeMushroom
if($user['birthday'])
{
	$trolls = array(
		"&#x2652; Eridan Ampora",
		"&#x2653; Feferi Peixes",
		"&#x2648; Aradia Megido",
		"&#x2649; Tavros Nitram",
		"&#x264A; Sollux Captor",
		"&#x264B; Karkat Vantas",
		"&#x264C; Nepeta Leijon",
		"&#x264D; Kanaya Maryam",
		"&#x264E; Terezi Pyrope",
		"&#x264F; Vriska Serket",
		"&#x2650; Equius Zahhak",
		"&#x2651; Gamzee Makara",
	);
	$tooltips = array(
		"caligulasAquarium, Aquarius",
		"cuttlefishCuller, Pisces",
		"apocalypseArisen, Aries",
		"adiosToreador, Taurus",
		"twinArmageddons, Gemini",
		"carcinoGeneticist, Cancer",
		"arsenicCatnip, Leo",
		"grimAuxiliatrix, Virgo",
		"gallowsCalibrator, Libra",
		"arachnidsGrip, Scorpio",
		"centaursTesticle, Saggitarius",
		"terminallyCapricious, Capricorn",
	);
	$colors = array(
		"#6A006A",
		"#77003C",
		"#A10000",
		"#C15000",
		"#C1A100",
		"#626262",
		"#416600",
		"#008141",
		"#008282",
		"#004193",
		"#000056",
		"#2B0057",
	);
	$dates = array(
		 120,
		 218,
		 320,
		 420,
		 521,
		 621,
		 722,
		 823,
		 923,
		1023,
		1122,
		1222,
	);

	$bday = (int)date("md", $user['birthday']);
	for($i = count($trolls) - 1; $i >= 0; $i--)
	{
		if($dates[$i] < $bday)
		{
			$profileParts['Miscellaneous']['Patron Troll'] = format("<span title=\"{1}\"><span style=\"color: {2}\"></span> {0}</span>", $trolls[$i], $tooltips[$i], $colors[$i]);
			break;
		}
	}
}

?>