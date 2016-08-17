<?php
//  AcmlmBoard XD support - Login support
if (!defined('BLARG')) die();

$bots = array(
	"Microsoft URL Control",
	"Yahoo! Slurp",
	"Twiceler",
	"facebook",
	"bot","spider","crawler", //catch-all
);

$isBot = 0;
if(str_replace($bots,"x",$_SERVER['HTTP_USER_AGENT']) != $_SERVER['HTTP_USER_AGENT']) // stristr()/stripos()?
	$isBot = 1;

include(__DIR__."/browsers.php");

//Check the amount of users right now for the records
$rMisc = Query("select * from {misc}");
$misc = Fetch($rMisc);

$rOnlineUsers = Query("select id from {users} where lastactivity > {0} or lastposttime > {0} order by name", (time()-300));

$_qRecords = "";
$onlineUsers = "";
$onlineUserCt = 0;
while($onlineUser = Fetch($rOnlineUsers))
{
	$onlineUsers .= ":".$onlineUser["id"];
	$onlineUserCt++;
}

if($onlineUserCt > $misc['maxusers'])
{
	$_qRecords = "maxusers = {0}, maxusersdate = {1}, maxuserstext = {2}";
}
//Check the amount of posts for the record
$newToday = FetchResult("select count(*) from {posts} where date > {0}", (time() - 86400));
$newLastHour = FetchResult("select count(*) from {posts} where date > {0}", (time() - 3600));
if($newToday > $misc['maxpostsday'])
{
	if($_qRecords) $_qRecords .= ", ";
	$_qRecords .= "maxpostsday = {3}, maxpostsdaydate = {1}";
}
if($newLastHour > $misc['maxpostshour'])
{
	if($_qRecords) $_qRecords .= ", ";
	$_qRecords .= "maxpostshour = {4}, maxpostshourdate = {1}";
}
if($_qRecords)
{
	$_qRecords = "update {misc} set ".$_qRecords;
	$rRecords = Query($_qRecords, $onlineUserCt, time(), $onlineUsers, $newToday, $newLastHour);
}

//Delete oldies visitor from the guest list. We may re-add him/her later.
Query("delete from {guests} where date < {0}", (time()-300));

//Lift dated Tempbans
Query("update {users} set primarygroup = tempbanpl, tempbantime = 0, title='' where tempbantime != 0 and tempbantime < {0}", time());

//Lift dated IP Bans
Query("delete from {ipbans} where date != 0 and date < {0}", time());

//Delete expired sessions
Query("delete from {sessions} where expiration != 0 and expiration < {0}", time());

function isIPBanned($ip)
{
	$rIPBan = Query("select * from {ipbans} where instr({0}, ip)=1", $ip);
	
	while ($ipban = Fetch($rIPBan))
	{
		// check if this IP ban is actually good
		// if the last character is a number, IPs have to match precisely
		if (ctype_alnum(substr($ipban['ip'],-1)) && ($ip !== $ipban['ip']))
			continue;
		
		return $ipban;
	}
	return false;
}

$ipban = isIPBanned($_SERVER['REMOTE_ADDR']);

if($ipban)
{
	$adminemail = Settings::get('ownerEmail');
	
	print "You have been IP-banned from this board".($ipban['date'] ? " until ".gmdate("M jS Y, G:i:s",$ipban['date'])." (GMT). That's ".TimeUnits($ipban['date']-time())." left" : "").". Attempting to get around this in any way will result in worse things.";
	print '<br>Reason: '.$ipban['reason'];
	if ($adminemail) print '<br><br>If you were erroneously banned, contact the board owner at: '.$adminemail;
	exit();
}

function doHash($data)
{
	return hash('sha256', $data, FALSE);
}

$loguser = NULL;

if($_COOKIE['logsession'] && !$ipban)
{
	$session = Fetch(Query("SELECT * FROM {sessions} WHERE id={0}", doHash($_COOKIE['logsession'].SALT)));
	if($session)
	{
		$loguser = Fetch(Query("SELECT * FROM {users} WHERE id={0}", $session["user"]));
		if($session["autoexpire"])
			Query("UPDATE {sessions} SET expiration={0} WHERE id={1}", time()+10*60, $session["id"]); //10 minutes
	}
}

if($loguser)
{
	$loguser['token'] = hash('sha1', "{$loguser['id']},{$loguser['pss']},".SALT.",dr567hgdf546guol89ty896rd7y56gvers9t");
	$loguserid = $loguser["id"];
	
	$sessid = doHash($_COOKIE['logsession'].SALT);
	Query("UPDATE {sessions} SET lasttime={0} WHERE id={1}", time(), $sessid);
	Query("DELETE FROM {sessions} WHERE user={0} AND lasttime<={1}", $loguserid, time()-2592000);
}
else
{
	$loguser = array("name"=>"", "primarygroup"=>Settings::get('defaultGroup'), "threadsperpage"=>50, "postsperpage"=>20, "theme"=>Settings::get("defaultTheme"),
		"dateformat"=>"m-d-y", "timeformat"=>"h:i A", "fontsize"=>80, "timezone"=>0, "blocklayouts"=>!Settings::get("guestLayouts"),
		'token'=>hash('sha1', rand()));
	$loguserid = 0;
}

if ($loguser['flags'] & 0x1)
{
	Query("INSERT INTO {ipbans} (ip,reason,date) VALUES ({0},{1},0)",
		$_SERVER['REMOTE_ADDR'], '['.htmlspecialchars($loguser['name']).'] Account IP-banned');
	die(header('Location: '.$_SERVER['REQUEST_URI']));
}

if ($mobileLayout)
{
	$loguser['blocklayouts'] = 1;
	$loguser['fontsize'] = 80;
	//$loguser['dateformat'] = 'm/d/y';
	//$loguser['timeformat'] = 'H:i';
}


function setLastActivity()
{
	global $loguserid, $isBot, $lastKnownBrowser, $ipban;

	Query("delete from {guests} where ip = {0}", $_SERVER['REMOTE_ADDR']);

	if($ipban) return;

	if($loguserid == 0)
	{
		$ua = "";
		if(isset($_SERVER['HTTP_USER_AGENT']))
			$ua = $_SERVER['HTTP_USER_AGENT'];
		Query("insert into {guests} (date, ip, lasturl, useragent, bot) values ({0}, {1}, {2}, {3}, {4})",
			time(), $_SERVER['REMOTE_ADDR'], getRequestedURL(), $ua, $isBot);
	}
	else
	{
		Query("update {users} set lastactivity={0}, lastip={1}, lasturl={2}, lastknownbrowser={3}, loggedin=1 where id={4}",
			time(), $_SERVER['REMOTE_ADDR'], getRequestedURL(), $lastKnownBrowser, $loguserid);
	}
}

?>
