<?php
if (!defined('BLARG')) die();

$userMenu = array();

if($loguserid)
{
	if (HasPermission('user.editprofile'))
	{
		$userMenu[actionLink('editprofile')] = __('Edit profile');
		if (HasPermission('user.editavatars'))
			$userMenu[actionLink('editavatars')] = __('Mood avatars');
	}
	
	$userMenu[actionLink('private')] = __('Private messages');
	$userMenu[actionLink('favorites')] = __('Favorites');

	$bucket = 'userMenu'; include(__DIR__."/../lib/pluginloader.php");
}

$layout_userpanel = $userMenu;
?>
