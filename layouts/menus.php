<?php
if (!defined('BLARG')) die();

$headerlinks = [];

$sidelinks = [
	Settings::get('menuMainName') => [
		actionLink('home') => [
			'text' => 'Home Page',
			'icon' => 'home'
		],
		actionLink('board') => [
			'text' => 'Forum'
		],
		actionLink('FAQ') => [
			'text' => 'FAQ',
			'icon' => 'question'
		],
		actionLink('memberlist') => [
			'text' => 'Member list',
			'icon' => 'group'
		],
		actionLink('online') => [
			'text' => 'Online Users',
			'icon' => 'eye-open'
		],
		actionLink('ranks') => [
			'text' => 'Ranks',
			'icon' => 'trophy'
		],
		actionLink('lastposts') => [
			'text' => 'Last Posts',
			'icon' => 'reorder'
		],
		actionLink('search') => [
			'text' => 'Search',
			'icon' => 'search'
		],
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
