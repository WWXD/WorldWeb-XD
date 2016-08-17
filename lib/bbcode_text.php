<?php
if (!defined('BLARG')) die();

// Misc things that get replaced in text.

function loadSmilies()
{
	global $smilies, $smiliesReplaceOrig, $smiliesReplaceNew;

	$rSmilies = Query("select * from {smilies} order by length(code) desc");
	$smilies = array();

	while($smiley = Fetch($rSmilies))
		$smilies[] = $smiley;

	$smiliesReplaceOrig = $smiliesReplaceNew = array();
	for ($i = 0; $i < count($smilies); $i++)
	{
		$smiliesReplaceOrig[] = "/(?<!\w)".preg_quote($smilies[$i]['code'], "/")."(?!\w)/";
		$smiliesReplaceNew[] = "<img class=\"smiley\" alt=\"\" src=\"".resourceLink("img/smilies/".$smilies[$i]['image'])."\" />";
	}
}

// lol
function funhax($s)
{
	return 'DU'.str_repeat('R', strlen($s[0])-2);
}

function rainbowify($s)
{
	$r = mt_rand(0,359);
	$len = strlen($s);
	$out = '';
	for ($i = 0; $i < $len; $i++)
	{
		if ($s[$i] == ' ')
		{
			$out .= ' ';
			continue;
		}
		
		$out .= '<span style="color:hsl('.$r.',100%,80.4%);">'.$s[$i].'</span>';
		$r += 31;
		$r %= 360;
	}
	return $out;
}

//Main post text replacing.
function postDoReplaceText($s, $parentTag, $parentMask)
{
	global $postNoSmilies, $postPoster, $smiliesReplaceOrig, $smiliesReplaceNew;

	if($postPoster)
		$s = preg_replace("'/me '","<b>* ".htmlspecialchars($postPoster)."</b> ", $s);
		
	// silly filters
	//$s = preg_replace_callback('@\._+\.@', 'funhax', $s);
	//$s = str_replace(':3', ':3 '.rainbowify('ALL THE INSULTS I JUST SAID NOW BECOME LITTLE COLOURFUL FLOWERS'), $s);

	//Smilies
	if(!$postNoSmilies)
	{
		if(!isset($smiliesReplaceOrig))
			LoadSmilies();
		$s = preg_replace($smiliesReplaceOrig, $smiliesReplaceNew, $s);
	}
	
	//Automatic links
	// does it really have to be that complex?! we're not phpBB
	//$s = preg_replace_callback('((?:(?:view-source:)?(?:[Hh]t|[Ff])tps?://(?:(?:[^:&@/]*:[^:@/]*)@)?|\bwww\.)[a-zA-Z0-9\-]+(?:\.[a-zA-Z0-9\-]+)*(?::[0-9]+)?(?:/(?:->(?=\S)|&amp;|[\w\-/%?=+#~:\'@*^$!]|[.,;\'|](?=\S)|(?:(\()|(\[)|\{)(?:->(?=\S)|[\w\-/%&?=+;#~:\'@*^$!.,;]|(?:(\()|(\[)|\{)(?:->(?=\S)|l[\w\-/%&?=+;#~:\'@*^$!.,;])*(?(3)\)|(?(4)\]|\})))*(?(1)\)|(?(2)\]|\})))*)?)', 'bbcodeURLAuto', $s);
	if (!($parentMask & TAG_NOAUTOLINK))
	{
		$s = preg_replace_callback('@(?:(?:http|ftp)s?://|\bwww\.)[\w\-/%&?=+#~\'\@*^$\.,;!:]+[\w\-/%&?=+#~\'\@*^$]@i', 'bbcodeURLAuto', $s);
	}

	//Plugin bucket for allowing plugins to add replacements.
	$bucket = "postMangler"; include(__DIR__."/pluginloader.php");

	return $s;
}

?>
