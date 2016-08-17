<?php
//  AcmlmBoard XD - Thread display page
//  Access: all
if (!defined('BLARG')) die();


if(isset($_GET['pid']))
{
	header("HTTP/1.1 301 Moved Permanently");
	header("Status: 301 Moved Permanently");
	die(header("Location: ".actionLink("post", $_GET["pid"])));
}

$tid = (int)$_GET['id'];
$rThread = Query("select * from {threads} where id={0}", $tid);

if(NumRows($rThread))
	$thread = Fetch($rThread);
else
	Kill(__("Unknown thread ID."));

$fid = $thread['forum'];
$rFora = Query("select * from {forums} where id={0}", $fid);
if(NumRows($rFora))
	$forum = Fetch($rFora);
else
	Kill(__("Unknown forum ID."));
	
if (!HasPermission('forum.viewforum', $fid))
	Kill(__('You may not access this forum.'));


$threadtags = ParseThreadTags($thread['title']);
$title = $threadtags[0];
$urlname = HasPermission('forum.viewforum', $fid, true) ? $title : '';

if(isset($_GET['vote']))
{
	CheckPermission('user.votepolls');
	
	if(!$loguserid)
		Kill(__("You can't vote without logging in."));
	if($thread['closed'])
		Kill(__("Poll's closed!"));
	if(!$thread['poll'])
		Kill(__("This is not a poll."));
	if ($loguser['token'] != $_GET['token'])
		Kill(__("Invalid token."));

	$vote = (int)$_GET['vote'];

	$doublevote = FetchResult("select doublevote from {poll} where id={0}", $thread['poll']);
	$existing = FetchResult("select count(*) from {pollvotes} where poll={0} and choiceid={1} and user={2}", $thread['poll'], $vote, $loguserid);
	if($doublevote)
	{
		//Multivote.
		if ($existing)
			Query("delete from {pollvotes} where poll={0} and choiceid={1} and user={2}", $thread['poll'], $vote, $loguserid);
		else
			Query("insert into {pollvotes} (poll, choiceid, user) values ({0}, {1}, {2})", $thread['poll'], $vote, $loguserid);
	}
	else
	{
		//Single vote only?
		//Remove any old votes by this user on this poll, then add a new one.
		Query("delete from {pollvotes} where poll={0} and user={1}", $thread['poll'], $loguserid);
		if(!$existing)
			Query("insert into {pollvotes} (poll, choiceid, user) values ({0}, {1}, {2})", $thread['poll'], $vote, $loguserid);
	}
	
	$ref = $_SERVER['HTTP_REFERER'] ?: actionLink('thread', $tid, '', $urlname);
	die(header('Location: '.$ref));
}

$firstpost = FetchResult("SELECT pt.text FROM {posts} p LEFT JOIN {posts_text} pt ON pt.pid=p.id AND pt.revision=p.currentrevision WHERE p.thread={0} AND p.deleted=0 ORDER BY p.date ASC LIMIT 1", $tid);
if ($firstpost && $firstpost != -1)
{
	$firstpost = strip_tags($firstpost);
	$firstpost = preg_replace('@\[.*?\]@s', '', $firstpost);
	$firstpost = preg_replace('@\s+@', ' ', $firstpost);

	$firstpost = explode(' ', $firstpost);
	if (count($firstpost) > 30)
	{
		$firstpost = array_slice($firstpost, 0, 30);
		$firstpost[29] .= '...';
	}
	$firstpost = implode(' ', $firstpost);

	$metaStuff['description'] = htmlspecialchars($firstpost);
}
$metaStuff['tags'] = getKeywords(strip_tags($thread['title']));

Query("update {threads} set views=views+1 where id={0} limit 1", $tid);

$isold = (!$thread['sticky'] && Settings::get("oldThreadThreshold") > 0 && $thread['lastpostdate'] < time() - (2592000 * Settings::get("oldThreadThreshold")));

$links = array();
if ($loguserid)
{
	$notclosed = (!$thread['closed'] || HasPermission('mod.closethreads', $fid));
	
	if (HasPermission('forum.postreplies', $fid))
	{
		// allow the user to directly post in a closed thread if they have permission to open it
		if ($notclosed)
			$links[] = actionLinkTag(__("Post reply"), "newreply", $tid, '', $urlname);
		else if ($thread['closed'])
			$links[] = __("Thread closed");
	}
	
	if (FetchResult("SELECT COUNT(*) FROM {favorites} WHERE user={0} AND thread={1}", $loguserid, $tid) > 0)
		$links[] = actionLinkTag(__('Remove from favorites'), 'favorites', $tid, 'action=remove&token='.$loguser['token']);
	else
		$links[] = actionLinkTag(__('Add to favorites'), 'favorites', $tid, 'action=add&token='.$loguser['token']);

	// we also check mod.movethreads because moving threads is done on editthread
	if ((HasPermission('user.renameownthreads') && $thread['user']==$loguserid) || 
		(HasPermission('mod.renamethreads', $fid) || HasPermission('mod.movethreads', $fid))
		&& $notclosed)
		$links[] = actionLinkTag(__("Edit"), "editthread", $tid);
	
	if (HasPermission('mod.closethreads', $fid))
	{
		if($thread['closed'])
			$links[] = actionLinkTag(__("Open"), "editthread", $tid, "action=open&key=".$loguser['token']);
		else
			$links[] = actionLinkTag(__("Close"), "editthread", $tid, "action=close&key=".$loguser['token']);
	}
		
	if (HasPermission('mod.stickthreads', $fid))
	{
		if($thread['sticky'])
			$links[] = actionLinkTag(__("Unstick"), "editthread", $tid, "action=unstick&key=".$loguser['token']);
		else
			$links[] = actionLinkTag(__("Stick"), "editthread", $tid, "action=stick&key=".$loguser['token']);
	}

	if (HasPermission('mod.trashthreads', $fid) && Settings::get('trashForum'))
	{
		if($forum['id'] != Settings::get('trashForum'))
			$links[] = actionLinkTag(__("Trash"), "editthread", $tid, "action=trash&key=".$loguser['token']);
	}
	
	if (HasPermission('mod.deletethreads', $fid) && Settings::get('secretTrashForum'))
	{
		if ($forum['id'] != Settings::get('secretTrashForum'))
			$links[] = actionLinkTagConfirm(__("Delete"), __("Are you sure you want to just up and delete this whole thread?"), "editthread", $tid, "action=delete&key=".$loguser['token']);
	}
}

$OnlineUsersFid = $fid;
LoadPostToolbar();

MakeCrumbs(forumCrumbs($forum) + array(actionLink("thread", $tid, '', $urlname) => $threadtags[0]), $links);

if($thread['poll'])
{
	$poll = Fetch(Query("SELECT p.*,
							(SELECT COUNT(DISTINCT user) FROM {pollvotes} pv WHERE pv.poll = p.id) as users,
							(SELECT COUNT(*) FROM {pollvotes} pv WHERE pv.poll = p.id) as votes
						 FROM {poll} p
						 WHERE p.id={0}", $thread['poll']));
						 
	if(!$poll)
		Kill(__("Poll not found"));

	$totalVotes = $poll['users'];

	$rOptions = Query("SELECT pc.*,
							(SELECT COUNT(*) FROM {pollvotes} pv WHERE pv.poll = {0} AND pv.choiceid = pc.id) as votes,
							(SELECT COUNT(*) FROM {pollvotes} pv WHERE pv.poll = {0} AND pv.choiceid = pc.id AND pv.user = {1}) as myvote
					   FROM {poll_choices} pc
					   WHERE poll={0}", $thread['poll'], $loguserid);
	$pops = 0;
	$noColors = 0;
	$defaultColors = array(
				  "#0000B6","#00B600","#00B6B6","#B60000","#B600B6","#B66700","#B6B6B6",
		"#676767","#6767FF","#67FF67","#67FFFF","#FF6767","#FF67FF","#FFFF67","#FFFFFF",);
		
	$pdata = array();
	$pdata['question'] = htmlspecialchars($poll['question']);
	$pdata['options'] = array();

	while($option = Fetch($rOptions))
	{
		$odata = array();
		
		$odata['color'] = htmlspecialchars($option['color']);
		if($odata['color'] == '')
			$odata['color'] = $defaultColors[($option['id'] + 9) % 15];

		$chosen = $option['myvote']? '&#x2714;':'';

		if($loguserid && (!$thread['closed'] || HasPermission('mod.closethreads', $fid)) && HasPermission('user.votepolls'))
			$label = $chosen." ".actionLinkTag(htmlspecialchars($option['choice']), "thread", $thread['id'], "vote=".$option['id']."&token=".$loguser['token'], $urlname);
		else
			$label = $chosen." ".htmlspecialchars($option['choice']);
		$odata['label'] = $label;
			
		$odata['votes'] = $option['votes'];
		if($totalVotes > 0)
		{
			$width = (100 * $odata['votes']) / $totalVotes;
			$odata['percent'] = sprintf('%.4g', $width);
		}
		else
			$odata['percent'] = 0;

		$pdata['options'][] = $odata;
	}
	
	$pdata['multivote'] = $poll['doublevote'];
	$pdata['votes'] = $poll['votes'];
	$pdata['voters'] = $totalVotes;

	RenderTemplate('poll', array('poll' => $pdata));
}

Query("insert into {threadsread} (id,thread,date) values ({0}, {1}, {2}) on duplicate key update date={2}", $loguserid, $tid, time());

$total = $thread['replies'] + 1; //+1 for the OP
$ppp = $loguser['postsperpage'];
if(!$ppp) $ppp = 20;
if(isset($_GET['from']))
	$from = $_GET['from'];
else
	$from = 0;

$rPosts = Query("
			SELECT
				p.*,
				pt.text, pt.revision, pt.user AS revuser, pt.date AS revdate,
				u.(_userfields), u.(rankset,title,picture,posts,postheader,signature,signsep,lastposttime,lastactivity,regdate,globalblock,fulllayout),
				ru.(_userfields),
				du.(_userfields)
			FROM
				{posts} p
				LEFT JOIN {posts_text} pt ON pt.pid = p.id AND pt.revision = p.currentrevision
				LEFT JOIN {users} u ON u.id = p.user
				LEFT JOIN {users} ru ON ru.id=pt.user
				LEFT JOIN {users} du ON du.id=p.deletedby
			WHERE thread={1}
			ORDER BY date ASC LIMIT {2u}, {3u}", $loguserid, $tid, $from, $ppp);
$numonpage = NumRows($rPosts);

$pagelinks = PageLinks(actionLink("thread", $tid, "from=", $urlname), $ppp, $from, $total);

RenderTemplate('pagelinks', array('pagelinks' => $pagelinks, 'position' => 'top'));

if(NumRows($rPosts))
{
	while($post = Fetch($rPosts))
	{
		$post['closed'] = $thread['closed'];
		$post['firstpostid'] = $thread['firstpostid'];
		MakePost($post, POST_NORMAL, array('tid'=>$tid, 'fid'=>$fid));
	}
}

RenderTemplate('pagelinks', array('pagelinks' => $pagelinks, 'position' => 'bottom'));

if($loguserid && HasPermission('forum.postreplies', $fid) && !$thread['closed'] && !$isold)
{
	$ninja = FetchResult("select id from {posts} where thread={0} order by date desc limit 0, 1", $tid);

	$mod_lock = '';
	if (HasPermission('mod.closethreads', $fid))
	{
		if(!$thread['closed'])
			$mod_lock = "<label><input type=\"checkbox\" name=\"lock\">&nbsp;".__("Close thread", 1)."</label>\n";
		else
			$mod_lock = "<label><input type=\"checkbox\" name=\"unlock\">&nbsp;".__("Open thread", 1)."</label>\n";
	}
	
	$mod_stick = '';
	if (HasPermission('mod.stickthreads', $fid))
	{
		if(!$thread['sticky'])
			$mod_stick = "<label><input type=\"checkbox\" name=\"stick\">&nbsp;".__("Sticky", 1)."</label>\n";
		else
			$mod_stick = "<label><input type=\"checkbox\" name=\"unstick\">&nbsp;".__("Unstick", 1)."</label>\n";
	}
	
	$moodOptions = "<option selected=\"selected\" value=\"0\">".__("[Default avatar]")."</option>\n";
	$rMoods = Query("select mid, name from {moodavatars} where uid={0} order by mid asc", $loguserid);
	while($mood = Fetch($rMoods))
		$moodOptions .= format(
"
	<option value=\"{0}\">{1}</option>
",	$mood['mid'], htmlspecialchars($mood['name']));

	$fields = array(
		'text' => "<textarea id=\"text\" name=\"text\" rows=\"8\"></textarea>",
		'mood' => "<select size=1 name=\"mood\">".$moodOptions."</select>",
		'nopl' => "<label><input type=\"checkbox\" name=\"nopl\">&nbsp;".__("Disable post layout", 1)."</label>",
		'nosm' => "<label><input type=\"checkbox\" name=\"nosm\">&nbsp;".__("Disable smilies", 1)."</label>",
		'lock' => $mod_lock,
		'stick' => $mod_stick,
		
		'btnPost' => "<input type=\"submit\" name=\"actionpost\" value=\"".__("Post")."\">",
		'btnPreview' => "<input type=\"submit\" name=\"actionpreview\" value=\"".__("Preview")."\">",
	);

	echo "
	<form action=\"".htmlentities(actionLink("newreply", $tid))."\" method=\"post\">
		<input type=\"hidden\" name=\"ninja\" value=\"{$ninja}\">";
	
	RenderTemplate('form_quickreply', array('fields' => $fields));

	echo "
	</form>";
}

?>
