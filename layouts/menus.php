<?php
if (!defined('BLARG')) die();

$headerlinks = [
	pageLink('irc') => 'IRC',
];

$sidelinks = [
	Settings::get('menuMainName') => [
		actionLink('home') => 'Home page',
		actionLink('board') => 'Forums',
		actionLink('faq') => 'FAQ',
		actionLink('memberlist') => 'Member list',
		actionLink('ranks') => 'Ranks',
		actionLink('online') => 'Online users',
		actionLink('lastposts') => 'Last posts',
		actionLink('search') => 'Search',
	],
];

$dropdownlinks = [
	Settings::get('menuMainName') => [
		actionLink('board') => 'Index',
		actionLink('faq') => 'FAQ',
		actionLink('memberlist') => 'Member list',
		actionLink('ranks') => 'Ranks',
		actionLink('online') => 'Online users',
		actionLink('lastposts') => 'Last posts',
		actionLink('search') => 'Search',
	],
];

$bucket = "headerlinks"; include(BOARD_ROOT."lib/pluginloader.php");
$bucket = "sidelinks"; include(BOARD_ROOT."lib/pluginloader.php");
$bucket = "dropdownlinks"; include(BOARD_ROOT."lib/pluginloader.php");

?>
