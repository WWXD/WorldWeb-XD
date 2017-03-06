<?php
//  AcmlmBoard XD support - Login support
if (!defined('BLARG')) die();

$bots = array(
	"Microsoft URL Control", "Bingbot", "Microsoft URL Control - 5.01.4511", "Microsoft URL Control - 6.00.8169",
	"Yahoo! Slurp", "Slurp",
	"Mediapartners-Google", "AdsBot-Google-Mobile-Apps", "Googlebot-News", "Googlebot-Image", "GoogleBot", "AdsBot-Google", "AdsBot-Google-Mobile-Apps",
	"Twiceler",
	"facebook", "facebookexternalhit",
	"DuckDuckBot",
	"Baiduspider", "Baiduspider-ads", "Baiduspider-cpro", "	Baiduspider-favo", "Baiduspider-news", "Baiduspider-video", "Baiduspider-image",
	"YandexBot",
	"Sogou Pic Spider", "Sogou head spider", "Sogou web spider", "Sogou Orion spider", "Sogou-Test-Spider",
	"ia_archiver",
	"catchbot",
	"Gigabot",
	"asterias",
	"BackDoorBot", "BackDoorBot/1.0",
	"Black Hole",
	"BlowFish", "BlowFish/1.0",
	"BotALot",
	"BuiltBotTough",
	"Bullseye/1.0", "Bullseye",
	"BunnySlippers",
	"Cegbfeieh",
	"CheeseBot",
	"CherryPicker", "CherryPickerElite", "CherryPickerSE", "CherryPickerElite/1.0", "CherryPickerSE/1.0",
	"CopyRightCheck",
	"cosmos",
	"Crescent",
	"Crescent Internet ToolPak HTTP OLE Control v.1.0", "Crescent Internet ToolPak HTTP OLE Control",
	"DittoSpyder",
	"EmailCollector",
	"EmailSiphon",
 "EmailWolf",
 "EroCrawler",
 "ExtractorPro",
 "Foobot",
 "Harvest/1.5",
 "hloader",
 "httplib",
 "humanlinks",
 "InfoNaviRobot",
 "JennyBot",
 "Kenjin Spider",
 "Keyword Density/0.9",
	"LexiBot",
 "libWeb/clsHTTP",
 "linkextractorPro",
 "linkScan/8.1a Unix",
 "linkWalker",
 "lNSpiderguy",
 "lwp-trivial",
 "lwp-trivial/1.34",
 "Mata Hari",
 "MIIxpc",
 "MIIxpc/4.2",
 "Mister PiX",
 "moget", "moget/2.1",
 "mozilla/4", "mozilla/4.0 (compatible; BullsEye; Windows 95)", "mozilla/4.0 (compatible; MSIE 4.0; Windows 95)", "mozilla/4.0 (compatible; MSIE 4.0; Windows 98)", "mozilla/4.0 (compatible; MSIE 4.0; Windows NT)", "mozilla/4.0 (compatible; MSIE 4.0; Windows XP)", "mozilla/4.0 (compatible; MSIE 4.0; Windows 2000)", "mozilla/4.0 (compatible; MSIE 4.0; Windows ME)", "mozilla/5",
 "NetAnts",
 "NICErsPRO",
 "Offline Explorer",
 "Openfind", "Openfind data gathere",
 "ProPowerBot/2.14",
 "ProWebWalker",
 "QueryN Metasearch",
 "RepoMonkey", "RepoMonkey Bait & Tackle/v1.01",
 "RMA",
 "SiteSnagger",
 "SpankBot",
 "spanner",
 "suzuran",
 "Szukacz/1.4",
 "Teleport",
 "TeleportPro",
 "Telesoft",
 "The Intraformant",
 "TheNomad",
 "TightTwatBot",
 "Titan",
 "ToCrawl/UrlDispatcher",
 "True_Robot", "True_Robot/1.0",
 "Turingos",
 "URLy Warning",
 "VCI",
 "VCI WebViewer VCI WebViewer Win32",
	"Web Image Collector",
	"WebAuto",
	"WebBandit",
	"WebBandit/3.50",
	"WebCopier",
	"WebEnhancer",
	"WebmasterWorldForumBot",
	"WebSauger",
	"Website Quester",
	"Webster Pro",
	"WebStripper",
	"WebZip",
	"WebZip/4.0",
	"Wget",
	"Wget/1.5.3",
	"Wget/1.6",
	"WWW-Collector-E",
 "Xenu's",
 "Xenu's Link Sleuth 1.1c",
 "Zeus",
 "Zeus 32297 Webster Pro V2.9 Win32",
	"bot", "spider", "crawler", //catch-all
);

$isBot = 0;
if(str_replace($bots,"x",$_SERVER['HTTP_USER_AGENT']) != $_SERVER['HTTP_USER_AGENT']) // stristr()/stripos()?
	$isBot = 1;

//Check the amount of users right now for the records
$rMisc = Query("select * from {misc}");
$misc = Fetch($rMisc);

$rOnlineUsers = Query("select id from {users} where lastactivity > {0} or lastposttime > {0} order by name", (time()-300));

$_qRecords = "";
$onlineUsers = "";
$onlineUserCt = 0;
while($onlineUser = Fetch($rOnlineUsers)) {
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

function isIPBanned($ip) {
	$rIPBan = Query("select * from {ipbans} where instr({0}, ip)=1", $ip);
	
	$result = false;
	while ($ipban = Fetch($rIPBan)) {
		// check if this IP ban is actually good
		// if the last character is a number, IPs have to match precisely
		if (ctype_alnum(substr($ipban['ip'],-1)) && ($ip !== $ipban['ip']))
			continue;

		return $ipban;

		if (IPMatches($ip, $ipban['ip']))
			if ($ipban['whitelisted'])
				return false;
			else
				$result = $ipban;
	}
	return $result;
}

function IPMatches($ip, $mask) {
	return $ip === $mask || $mask[strlen($mask) - 1] === '.';
}

$ipban = isIPBanned($_SERVER['REMOTE_ADDR']);

if($ipban)
	$_GET["page"] = "ipbanned";

function doHash($data)
{
	return hash('sha256', $data, FALSE);
}

$loguser = NULL;

if($_COOKIE['logsession'] && !$ipban) {
	$session = Fetch(Query("SELECT * FROM {sessions} WHERE id={0}", doHash($_COOKIE['logsession'].SALT)));
	if($session) {
		$loguser = Fetch(Query("SELECT * FROM {users} WHERE id={0}", $session["user"]));
		if($session["autoexpire"])
			Query("UPDATE {sessions} SET expiration={0} WHERE id={1}", time()+10*60, $session["id"]); //10 minutes
	}
}

if($loguser) {
	$loguser['token'] = hash('sha1', "{$loguser['id']},{$loguser['pss']},".SALT.",dr567hgdf546guol89ty896rd7y56gvers9t");
	$loguserid = $loguser["id"];

	$sessid = doHash($_COOKIE['logsession'].SALT);
	Query("UPDATE {sessions} SET lasttime={0} WHERE id={1}", time(), $sessid);
	Query("DELETE FROM {sessions} WHERE user={0} AND lasttime<={1}", $loguserid, time()-2592000);
} else {
	$loguser = array("name"=>"", "primarygroup"=>Settings::get('defaultGroup'), "threadsperpage"=>50, "postsperpage"=>20, "theme"=>Settings::get("defaultTheme"),
		"dateformat"=>"m-d-y", "timeformat"=>"h:i A", "fontsize"=>80, "timezone"=>0, "blocklayouts"=>!Settings::get("guestLayouts"),
		'token'=>hash('sha1', rand()));
	$loguserid = 0;
}

if ($loguser['flags'] & 0x1) {
	Query("INSERT INTO {ipbans} (ip,reason,date) VALUES ({0},{1},0)",
		$_SERVER['REMOTE_ADDR'], '['.htmlspecialchars($loguser['name']).'] Account IP-banned');
	die(header('Location: '.$_SERVER['REQUEST_URI']));
}

if ($mobileLayout) {
	$loguser['blocklayouts'] = 1;
	$loguser['fontsize'] = 80;
	//$loguser['dateformat'] = 'm/d/y';
	//$loguser['timeformat'] = 'H:i';
}


function setLastActivity() {
	global $loguserid, $isBot, $lastKnownBrowser, $ipban;

	Query("delete from {guests} where ip = {0}", $_SERVER['REMOTE_ADDR']);

	if($ipban) return;

	$url = getRequestedURL();
	$url = substr($url, 0, 127);

	if($loguserid == 0)
	{
		$ua = "";
		if(isset($_SERVER['HTTP_USER_AGENT']))
			$ua = $_SERVER['HTTP_USER_AGENT'];
		Query("insert into {guests} (date, ip, lasturl, useragent, bot) values ({0}, {1}, {2}, {3}, {4})",
			time(), $_SERVER['REMOTE_ADDR'], $url, $ua, $isBot);
	}
	else
	{
		Query("update {users} set lastactivity={0}, lastip={1}, lasturl={2}, lastknownbrowser={3}, loggedin=1 where id={4}",
			time(), $_SERVER['REMOTE_ADDR'], $url, $lastKnownBrowser, $loguserid);
	}
}