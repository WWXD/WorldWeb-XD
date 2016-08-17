<?php

$c1 = ircColor(Settings::pluginGet("color1"));
$c2 = ircColor(Settings::pluginGet("color2"));

$thename = $loguser["name"];
if($loguser["displayname"])
	$thename = $loguser["displayname"];
$uncolor = ircUserColor($thename, $loguser['sex'], 0);
	

if($urlRewriting)
	$link = getServerURLNoSlash().actionLink("wiki", $page['id'], '', '_');
else
	$link = getServerURL()."?page=wiki&id=".$page['id'];

if ($page['new'] == 2)
	ircReport("\003{$c2}New wiki page: \003{$c1}".url2title($page['id'])." \003{$c2}created by \003{$c1}{$uncolor} \003{$c2} -- {$link}");
else
	ircReport("\003{$c2}Wiki page \003{$c1}".url2title($page['id'])." \003{$c2}edited by \003{$c1}{$uncolor} \003{$c2}(rev. {$rev}) -- {$link}");
	
?>
