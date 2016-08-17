<?php

$canhavenamecolor = HasPermission('user.editnamecolor') || $editUserMode;

if(!function_exists("HandleUsernameColor"))
{
	function HandleUsernameColor($field, $item)
	{
		global $user, $canhavenamecolor;

		if ($canhavenamecolor)
		{
			$unc = $_POST['color'];
			if($unc != '')
				$unc = filterPollColors(str_pad($unc, 6, '0'));

			Query("UPDATE {users} SET color={0s} WHERE id={1}", $unc, $user['id']);
		}
		return true;
	}
}

if ($canhavenamecolor)
{
	AddField('general', 'appearance', 'color', __('Name color'), 'color', 
		array('hint'=>__('Leave empty to use the default color.'), 'callback'=>'HandleUsernameColor'));
}


