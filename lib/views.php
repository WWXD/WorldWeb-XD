<?php
//  AcmlmBoard XD support - View counter support
if (!defined('BLARG')) die();

//Update view counter
if(!$isBot)
{
	$rViewCounter = Query("update {misc} set views = views + 1");
	$misc['views']++;

	$viewcountInterval = Settings::get("viewcountInterval");
	//Milestone reporting
	if($viewcountInterval > 0 && $misc['views'] > 0 && $misc['views'] % $viewcountInterval == 0)
	{
		if($loguserid)
		{
			$who = UserLink($loguser); //$loguser['name'];
			//3.0 update: give a badge
			Query("insert ignore into {badges} values({0}, {1}, 0)", $loguserid, 'View '.number_format($misc['views']));
		}
		else
			$who = "a guest at ".$_SERVER['REMOTE_ADDR'];

		Query("update {misc} set milestone = {0}", 'View '.$misc['views'].' reached by '.$who);
	}
}

?>
