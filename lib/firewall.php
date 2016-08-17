<?php
if (!defined('BLARG')) die();

function do403()
{
	header('HTTP/1.1 403 Forbidden');
	header('Status: 403 Forbidden');
	die('403 Forbidden');
}

function do404()
{
	header('HTTP/1.1 404 Not Found');
	header('Status: 404 Not Found');
	die('404 Not Found');
}

// weird bots. Rumors say it's hacking bots, or the bots China uses to crawl the internet and censor it
// in either case we don't lose much by keeping them out
if ($_SERVER['HTTP_USER_AGENT'] == 'Mozilla/4.0')
	do403();

// spamdexing in referrals/useragents
if (stristr($_SERVER['HTTP_REFERER'], '<a href=') ||
	stristr($_SERVER['HTTP_USER_AGENT'], '<a href='))
	do403();

// spamrefreshing
if (stristr($_SERVER['HTTP_REFERER'], 'refreshthis.com'))
	do403();

if ($isBot)
{
	// keep SE bots out of certain pages that don't interest them anyway
	// TODO move that code to those individual pages
	$forbidden = array('register', 'login', 'online', 'referrals', 'records', 'lastknownbrowsers');
	if (in_array($_GET['page'], $forbidden))
		do403();
}

?>