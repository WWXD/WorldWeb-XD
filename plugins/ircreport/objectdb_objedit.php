<?php

$c1 = ircColor(Settings::pluginGet("color1"));
$c2 = ircColor(Settings::pluginGet("color2"));

$thename = $loguser["name"];
if($loguser["displayname"])
	$thename = $loguser["displayname"];
$uncolor = ircUserColor($thename, $loguser['sex'], 0);
	

if($urlRewriting)
	$link = getServerURLNoSlash().actionLink("objectdbchanges", $sprite['id'], 'rev='.$rev, '_');
else
	$link = getServerURL()."?page=objectdbchanges&id={$sprite['id']}&rev={$rev}";

ircReport("\003{$c2}Object \003{$c1}{$spritename} ({$sprite['id']}) \003{$c2}edited by \003{$c1}{$uncolor} \003{$c2}(rev. {$rev}) -- {$link}");
	
?>
