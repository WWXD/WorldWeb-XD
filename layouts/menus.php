<?php
if (!defined('BLARG')) die();

$headerlinks = array
(
	actionLink('put your links here') => 'Put what you want it to be called here', 
);

$sidelinks = array
(
	Settings::get('menuMainName') => array
	(
		actionLink('home') => 'Home page',
		actionLink('board') => 'Forums',
		actionLink('faq') => 'FAQ',
		actionLink('memberlist') => 'Member list',
		actionLink('ranks') => 'Ranks',
		actionLink('online') => 'Online users',
		actionLink('lastposts') => 'Last posts',
		actionLink('search') => 'Search',
	),
);

$dropdownlinks = array
(
	Settings::get('menuMainName') => array
	(
		actionLink('board') => 'Index',
		actionLink('faq') => 'FAQ',
		actionLink('memberlist') => 'Member list',
		actionLink('ranks') => 'Ranks',
		actionLink('online') => 'Online users',
		actionLink('lastposts') => 'Last posts',
		actionLink('search') => 'Search',
	),
);

?>
