<?php

$redirects = Settings::pluginGet("redirects");

$redirects = explode("\n", $redirects);

//Remove empty strings
$redirects = array_map('trim', $redirects);
$redirects = array_filter($redirects);

foreach($redirects as $redirect)
{
	$redirect = explode(" ", $redirect);

	//Remove empty strings
	$redirect = array_map('trim', $redirect);
	$redirect = array_filter($redirect);
	
	$tableLists[$redirect[0]] = $redirect[1];
}

