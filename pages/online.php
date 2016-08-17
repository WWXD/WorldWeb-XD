<?php
//  AcmlmBoard XD - Realtime visitor statistics page
//  Access: all
if (!defined('BLARG')) die();

$title = __("Online users");
MakeCrumbs(array(actionLink("online") => __("Online users")));

$showIPs = HasPermission('admin.viewips');

$time = (int)$_GET['time'];
if(!$time) $time = 300;

$rUsers = Query("select * from {users} where lastactivity > {0} order by lastactivity desc", (time()-$time));
$rGuests = Query("select * from {guests} where date > {0} and bot = 0 order by date desc", (time()-$time));
$rBots = Query("select * from {guests} where date > {0} and bot = 1 order by date desc", (time()-$time));

$spans = array(60, 300, 900, 3600, 86400);
$spanList = array();
foreach($spans as $span)
{
	$spanList[] = ($span==$time) ? timeunits($span) : actionLinkTag(timeunits($span), "online", "", "time=$span");
}


$userList = array();
$i = 1;
while($user = Fetch($rUsers))
{
	$udata = array();
	$udata['num'] = $i++;
	
	$udata['link'] = UserLink($user);
	
	$udata['lastPost'] = ($user['lastposttime'] ? cdate("d-m-y G:i:s",$user['lastposttime']) : __("Never"));
	$udata['lastView'] = cdate("d-m-y G:i:s", $user['lastactivity']);
	
	if($user['lasturl'])
		$udata['lastURL'] = "<a href=\"".FilterURL($user['lasturl'])."\">".FilterURL($user['lasturl'])."</a>";
	else
		$udata['lastURL'] = __("None");
		
	if ($showIPs) $udata['ip'] = formatIP($user['lastip']);

	$userList[] = $udata;
}


$guestList = listGuests($rGuests);
$botList = listGuests($rBots);

RenderTemplate('onlinelist', array(
	'timelinks' => $spanList,
	'showIPs' => $showIPs, 
	'users' => $userList, 
	'guests' => $guestList, 
	'bots' => $botList));


function FilterURL($url)
{
	//$url = str_replace('_', ' ', urldecode($url)); // what?
	$url = htmlspecialchars($url);
	$url = preg_replace("@(&amp;)?(key|token)=[0-9a-f]{40,64}@i", '', $url);
	return $url;
}

function listGuests($rGuests)
{
	global $showIPs;
	
	$guestList = array();
	$i = 1;
	while($guest = Fetch($rGuests))
	{
		$gdata = array();
		$gdata['num'] = $i++;
		
		if ($showIPs)
			$gdata['userAgent'] = '<span title="'.htmlspecialchars($guest['useragent']).'">'.htmlspecialchars(substr($guest['useragent'],0,65)).'</span>';
		
		$gdata['lastView'] = cdate("d-m-y G:i:s", $guest['date']);
		
		if($guest['date'])
			$gdata['lastURL'] = "<a href=\"".FilterURL($guest['lasturl'])."\">".FilterURL($guest['lasturl'])."</a>";
		else
			$gdata['lastURL'] = __("None");
			
		if ($showIPs) $gdata['ip'] = formatIP($guest['ip']);

		$guestList[] = $gdata;
	}
	
	return $guestList;
}

?>
