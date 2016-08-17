<?php

if(!function_exists("MakeTrope"))
{
	function MakeTrope($matches)
	{
		$tropeTitle = $matches[1];
		$tropeText = $matches[2];
		return format("<a href=\"http://tvtropes.org/pmwiki/pmwiki.php/Main/{0}\">{1}</a>", $tropeTitle, $tropeText);
	}

	function MakeShortTrope($matches)
	{
		$tropeTitle = $matches[1];
		$tropeText = "";
		for($i = 0; $i < strlen($tropeTitle); $i++)
		{
			if(ctype_upper($tropeTitle[$i]))
				$tropeText .= " ";
			$tropeText .= $tropeTitle[$i];
		}
		$tropeText = trim($tropeText);
		return format("<a href=\"http://tvtropes.org/pmwiki/pmwiki.php/Main/{0}\">{1}</a>", $tropeTitle, $tropeText);
	}

	function MakeWikipedia($matches)
	{
		$wikiTitle = $matches[2];
		$escaped = str_replace(" ", "_", $matches[1]);
		return format("<a href=\"http://en.wikipedia.org/wiki/{0}\">{1}</a>", $escaped, $wikiTitle);
	}

	function MakeShortWikipedia($matches)
	{
		$wikiTitle = $matches[1];
		$escaped = str_replace(" ", "_", $wikiTitle);
		return format("<a href=\"http://en.wikipedia.org/wiki/{0}\">{1}</a>", $escaped, $wikiTitle);
	}
}

$s = preg_replace_callback("'\[wiki=(.*?)\](.*?)\[/wiki\]'si", "MakeWikipedia", $s);
$s = preg_replace_callback("'\[wiki\](.*?)\[/wiki\]'si", "MakeShortWikipedia", $s);
$s = preg_replace_callback("'\[trope=(.*?)\](.*?)\[/trope\]'si", "MakeTrope", $s);
$s = preg_replace_callback("'\[trope\](.*?)\[/trope\]'si", "MakeShortTrope", $s);

?>