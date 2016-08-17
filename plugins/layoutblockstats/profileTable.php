<?php

if ($user['id'] == $loguserid)// || $loguser["powerlevel"] >= 3)
{
	if (!$GLOBALS["myblockcount"])
		$GLOBALS["myblockcount"] = 1+FetchResult("SELECT COUNT(*) FROM blockedlayouts WHERE user={0}", $user["id"]);
	
	$profileParts[__('Personal information')][__('Layout blocks')] = 
			($GLOBALS["myblockcount"]-1).($GLOBALS["myblockcount"]==2 ? ' user has':' users have')." blocked ".
			($user['id'] == $loguserid ? "your" : "this user's")." layout<br />";
}


