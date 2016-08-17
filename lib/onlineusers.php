<?php
if (!defined('BLARG')) die();

function OnlineUsers($forum = 0, $update = true)
{
	global $loguserid;
	$forumClause = "";
	$browseLocation = __("online");

	if ($update)
	{
		if ($loguserid)
			Query("UPDATE {users} SET lastforum={0} WHERE id={1}", $forum, $loguserid);
		else
			Query("UPDATE {guests} SET lastforum={0} WHERE ip={1}", $forum, $_SERVER['REMOTE_ADDR']);
	}

	if($forum)
	{
		$forumClause = " and lastforum={1}";
		$forumName = FetchResult("SELECT title FROM {forums} WHERE id={0}", $forum);
		$browseLocation = format(__("browsing {0}"), $forumName);
	}

	$rOnlineUsers = Query("select u.(_userfields) from {users} u where (lastactivity > {0} or lastposttime > {0}) and loggedin = 1 ".$forumClause." order by name", time()-300, $forum);
	$onlineUserCt = 0;
	$onlineUsers = "";
	while($user = Fetch($rOnlineUsers))
	{
		$user = getDataPrefix($user, "u_");
		$userLink = UserLink($user, true);
		$onlineUsers .= ($onlineUserCt ? ", " : "").$userLink;
		$onlineUserCt++;
	}
	//$onlineUsers = $onlineUserCt." "user".(($onlineUserCt > 1 || $onlineUserCt == 0) ? "s" : "")." ".$browseLocation.($onlineUserCt ? ": " : ".").$onlineUsers;
	$onlineUsers = Plural($onlineUserCt, __("user"))." ".$browseLocation.($onlineUserCt ? ": " : ".").$onlineUsers;

	$data = Fetch(Query("select 
		(select count(*) from {guests} where bot=0 and date > {0} $forumClause) as guests,
		(select count(*) from {guests} where bot=1 and date > {0} $forumClause) as bots
		", (time() - 300), $forum));
	$guests = $data["guests"];
	$bots = $data["bots"];

	if($guests)
		$onlineUsers .= " | ".Plural($guests,__("guest"));
	if($bots)
		$onlineUsers .= " | ".Plural($bots,__("bot"));

//	$onlineUsers = "<div style=\"display: inline-block; height: 16px; overflow: hidden; padding: 0px; line-height: 16px;\">".$onlineUsers."</div>";
	return $onlineUsers;
}



function getOnlineUsersText()
{
	global $OnlineUsersFid;

	$refreshCode = "";

	if(!isset($OnlineUsersFid))
		$OnlineUsersFid = 0;

	if(Settings::get("ajax"))
	{
		$refreshCode = format(
	"
		<script type=\"text/javascript\">
			onlineFID = {0};
			window.addEventListener(\"load\",  startOnlineUsers, false);
		</script>
	", $OnlineUsersFid);
	}

	$onlineUsers = OnlineUsers($OnlineUsersFid);

	return "<div style=\"min-height:16px;\" id=\"onlineUsers\">$onlineUsers</div>$refreshCode";
}
?>
