<?php

$c1 = ircColor(Settings::pluginGet("color1"));
$c2 = ircColor(Settings::pluginGet("color2"));

$thename = $loguser["name"];
if($loguser["displayname"])
	$thename = $loguser["displayname"];
$uncolor = ircUserColor($thename, $loguser['sex'], 0);
	

if($urlRewriting)
	$link = getServerURLNoSlash().actionLink("objectdbchanges", $id, 'rev='.$previousrev, '_');
else
	$link = getServerURL()."?page=objectdbchanges&id={$id}&rev={$previousrev}";

$objname = FetchResult("SELECT s.name FROM {sprites} s LEFT JOIN {spriterevisions} sr ON sr.id=s.id AND sr.revision=s.revision WHERE s.id={0}", $id);
ircReport("\003{$c2}Object \003{$c1}{$objname} ({$id}) \003{$c2}reverted by \003{$c1}{$uncolor} \003{$c2}(rev. {$previousrev}) -- {$link}");
	
?>
