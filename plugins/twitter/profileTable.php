<?php

if(getSetting("twitName", true) != "")
{
	$twit = strip_tags(getSetting("twitName", true));
	$feed = @file_get_contents("http://search.twitter.com/search.atom?q=from%3A".$twit."&rpp=1");
	if($feed === FALSE)
		$result = "Could not get updates for ".$twit.".";
	else
	{
		$feed = substr($feed, strpos($feed, "<entry>"));
		preg_match("/\<content type=\"html\"\>(.*)\<\/content\>/", $feed, $matches2);
		preg_match("/\<updated\>(.*)\<\/updated\>/", $feed, $matches3);
		preg_match("/\<twitter:source\>(.*)\<\/twitter:source\>/", $feed, $matches4);

		$content = html_entity_decode($matches2[1]);
		$updateTime = formatdate(strtotime($matches3[1]));
		$source = html_entity_decode($matches4[1]);

		$result = $content." <small>(".$updateTime.", from ".$source.")</small>";
	}

	$profileParts[__('Personal information')][__('Last Tweet')] = $result;
}

?>
