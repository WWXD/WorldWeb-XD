<?php
//  AcmlmBoard XD - Thread editing page
//  Access: moderators
if (!defined('BLARG')) die();

$title = __("Edit thread");

if (isset($_REQUEST['action']) && $loguser['token'] != $_REQUEST['key'])
	Kill(__("No."));

if(!$loguserid) //Not logged in?
	Kill(__("You must be logged in to edit threads."));

if(isset($_POST['id']))
	$_GET['id'] = $_POST['id'];

if(!isset($_GET['id']))
	Kill(__("Thread ID unspecified."));

$tid = (int)$_GET['id'];

$rThread = Query("select * from {threads} where id={0}", $tid);
if(NumRows($rThread))
	$thread = Fetch($rThread);
else
	Kill(__("Unknown thread ID."));
	

$canClose = HasPermission('mod.closethreads', $thread['forum']);
$canStick = HasPermission('mod.stickthreads', $thread['forum']);
$canMove = HasPermission('mod.movethreads', $thread['forum']);
$isclosed = $thread['closed'] && !$canClose;
$canRename = ($thread['user'] == $loguserid && HasPermission('user.renameownthreads') && !$isclosed) || HasPermission('mod.renamethreads', $thread['forum']);

$canMod = $canRename || $canClose || $canStick || $canMove || HasPermission('mod.trashthreads', $thread['forum']) || HasPermission('mod.deletethreads', $thread['forum']);

if(!$canMod)
	Kill(__("You are not allowed to edit this thread."));

$rFora = Query("select * from {forums} where id={0}", $thread['forum']);
if(NumRows($rFora))
	$forum = Fetch($rFora);
else
	Kill(__("Unknown forum ID."));

if (!HasPermission('forum.viewforum', $forum['id']))
	Kill(__('You may not access this forum.'));

$OnlineUsersFid = $thread['forum'];
$isHidden = !HasPermission('forum.viewforum', $forum['id'], true);

$tags = ParseThreadTags($thread['title']);
$urlname = $isHidden?'':$tags[0];
MakeCrumbs(forumCrumbs($forum) + array(actionLink("thread", $tid, '', $urlname) => $tags[0], '' => __("Edit thread")));

$ref = $_SERVER['HTTP_REFERER'] ?: actionLink('thread', $tid, '', $urlname);

if($_GET['action']=="close" && $canClose)
{
	$rThread = Query("update {threads} set closed=1 where id={0}", $tid);
	Report("[b]".$loguser['name']."[/] closed thread [b]".$thread['title']."[/] -> [g]#HERE#?tid=".$tid, $isHidden);

	die(header("Location: ".$ref));
}
elseif($_GET['action']=="open" && $canClose)
{
	$rThread = Query("update {threads} set closed=0 where id={0}", $tid);
	Report("[b]".$loguser['name']."[/] opened thread [b]".$thread['title']."[/] -> [g]#HERE#?tid=".$tid, $isHidden);

	die(header("Location: ".$ref));
}
elseif($_GET['action']=="stick" && $canStick)
{
	$rThread = Query("update {threads} set sticky=1 where id={0}", $tid);
	Report("[b]".$loguser['name']."[/] stickied thread [b]".$thread['title']."[/] -> [g]#HERE#?tid=".$tid, $isHidden);

	die(header("Location: ".$ref));
}
elseif($_GET['action']=="unstick" && $canStick)
{
	$rThread = Query("update {threads} set sticky=0 where id={0}", $tid);
	Report("[b]".$loguser['name']."[/] unstuck thread [b]".$thread['title']."[/] -> [g]#HERE#?tid=".$tid, $isHidden);

	die(header("Location: ".$ref));
}
elseif(($_GET['action'] == "trash" && HasPermission('mod.trashthreads', $thread['forum']))
	|| ($_GET['action'] == 'delete' && HasPermission('mod.deletethreads', $thread['forum'])))
{
	if ($_GET['action'] == 'delete')
	{
		$trashid = Settings::get('secretTrashForum');
		$verb = 'deleted';
	}
	else
	{
		$trashid = Settings::get('trashForum');
		$verb = 'thrashed';
	}
	
	if($trashid > 0)
	{
		$rThread = Query("update {threads} set forum={0}, closed=1 where id={1} limit 1", $trashid, $tid);

		//Tweak forum counters
		$rForum = Query("update {forums} set numthreads=numthreads-1, numposts=numposts-{0} where id={1}", ($thread['replies']+1), $thread['forum']);
		$rForum = Query("update {forums} set numthreads=numthreads+1, numposts=numposts+{0} where id={1}", ($thread['replies']+1), $trashid);

		// Tweak forum counters #2
		Query("	UPDATE {forums} LEFT JOIN {threads}
				ON {forums}.id={threads}.forum AND {threads}.lastpostdate=(SELECT MAX(nt.lastpostdate) FROM {threads} nt WHERE nt.forum={forums}.id)
				SET {forums}.lastpostdate=IFNULL({threads}.lastpostdate,0), {forums}.lastpostuser=IFNULL({threads}.lastposter,0), {forums}.lastpostid=IFNULL({threads}.lastpostid,0)
				WHERE {forums}.id={0} OR {forums}.id={1}", $thread['forum'], $trashid);

		Report("[b]".$loguser['name']."[/] {$verb} thread [b]".$thread['title']."[/] -> [g]#HERE#?tid=".$tid, $isHidden);

		$forumname = '';
		if (HasPermission('forum.viewforum', $thread['forum'], true))
			$forumname = FetchResult("SELECT title FROM {forums} WHERE id={0}", $thread['forum']);
			
		die(header("Location: ".actionLink("forum", $thread['forum'], '', $forumname)));
	}
	else
		Kill(__("No trash forum set. Check board settings."));
}
elseif($_POST['actionedit'])
{
	if($thread['forum'] != $_POST['moveTo'] && $canMove)
	{
		$moveto = (int)$_POST['moveTo'];
		$dest = Fetch(Query("select * from {forums} where id={0}", $moveto));
		if(!$dest)
			Kill(__("Unknown forum ID."));
			
		$isHidden = HasPermission('forum.viewforum', $moveto, true);

		//Tweak forum counters
		$rForum = Query("update {forums} set numthreads=numthreads-1, numposts=numposts-{0} where id={1}", ($thread['replies']+1), $thread['forum']);
		$rForum = Query("update {forums} set numthreads=numthreads+1, numposts=numposts+{0} where id={1}", ($thread['replies']+1), $moveto);

		$rThread = Query("update {threads} set forum={0} where id={1}", (int)$_POST['moveTo'], $tid);

		// Tweak forum counters #2
		Query("	UPDATE {forums} LEFT JOIN {threads}
				ON {forums}.id={threads}.forum AND {threads}.lastpostdate=(SELECT MAX(nt.lastpostdate) FROM {threads} nt WHERE nt.forum={forums}.id)
				SET {forums}.lastpostdate=IFNULL({threads}.lastpostdate,0), {forums}.lastpostuser=IFNULL({threads}.lastposter,0), {forums}.lastpostid=IFNULL({threads}.lastpostid,0)
				WHERE {forums}.id={0} OR {forums}.id={1}", $thread['forum'], $moveto);

		Report("[b]".$loguser['name']."[/] moved thread [b]".$thread['title']."[/] -> [g]#HERE#?tid=".$tid, $isHidden);
	}

	$isClosed = $canClose ? (isset($_POST['isClosed']) ? 1 : 0) : $thread['closed'];
	$isSticky = $canStick ? (isset($_POST['isSticky']) ? 1 : 0) : $thread['sticky'];

	$trimmedTitle = $canRename ? trim(str_replace('&nbsp;', ' ', $_POST['title'])) : 'lolnotempty';
	if($trimmedTitle != "")
	{
		if ($canRename)
		{
			$thread['title'] = $_POST['title'];
			
			if($_POST['iconid'])
			{
				$_POST['iconid'] = (int)$_POST['iconid'];
				if($_POST['iconid'] < 255)
					$iconurl = "img/icons/icon".$_POST['iconid'].".png";
				else
					$iconurl = $_POST['iconurl'];
			}
		}
		else
			$iconurl = $thread['icon'];

		$rThreads = Query("update {threads} set title={0}, icon={1}, closed={2}, sticky={3} where id={4} limit 1", 
			$thread['title'], $iconurl, $isClosed, $isSticky, $tid);

		Report("[b]".$loguser['name']."[/] edited thread [b]".$thread['title']."[/] -> [g]#HERE#?tid=".$tid, $isHidden);
		
		$tags = ParseThreadTags($thread['title']);
		$urlname = $isHidden?'':$tags[0];
		$ref = $_POST['ref'] ?: actionLink('thread', $tid, '', $urlname);

		die(header("Location: ".$ref));
	}
	else
		Alert(__("Your thread title is empty. Enter a title and try again."));
}


$fields = array();

if ($canRename)
{
	$match = array();
	if (preg_match("@^img/icons/icon(\d+)\..{3,}\$@si", $thread['icon'], $match))
		$iconid = $match[1];
	elseif($thread['icon'] == "") //Has no icon
		$iconid = 0;
	else //Has custom icon
	{
		$iconid = 255;
		$iconurl = $thread['icon'];
	}

	if(!isset($iconid)) $iconid = 0;

	$icons = "";
	$i = 1;
	while(is_file("img/icons/icon".$i.".png"))
	{
		$check = "";
		if($iconid == $i) $check = "checked=\"checked\" ";
		$icons .= "	<label>
						<input type=\"radio\" $check name=\"iconid\" value=\"$i\">
						<img src=\"".resourceLink("img/icons/icon$i.png")."\" alt=\"Icon $i\" onclick=\"javascript:void()\">
					</label>";
		$i++;
	}
	$check[0] = "";
	$check[1] = "";
	if($iconid == 0) $check[0] = "checked=\"checked\" ";
	if($iconid == 255)
	{
		$check[1] = "checked=\"checked\" ";
	}

	$iconSettings = "
					<label>
						<input type=\"radio\" {$check[0]} name=\"iconid\" value=\"0\">
						<span>".__("None")."</span>
					</label>
					$icons
					<br/>
					<label>
						<input type=\"radio\" {$check[1]} name=\"iconid\" value=\"255\">
						<span>".__("Custom")."</span>
					</label>
					<input type=\"text\" name=\"iconurl\" size=60 maxlength=\"100\" value=\"".htmlspecialchars($iconurl)."\">";
					
	$fields['title'] = "<input type=\"text\" id=\"tit\" name=\"title\" size=80 maxlength=\"60\" value=\"".htmlspecialchars($thread['title'])."\">";
	$fields['icon'] = $iconSettings;
}

if ($canClose) $fields['closed'] = "<label><input type=\"checkbox\" name=\"isClosed\" ".($thread['closed'] ? " checked=\"checked\"" : "")."> ".__('Closed')."</label>";
if ($canStick) $fields['sticky'] = "<label><input type=\"checkbox\" name=\"isSticky\" ".($thread['sticky'] ? " checked=\"checked\"" : "")."> ".__('Sticky')."</label>";
if ($canMove) $fields['forum'] = makeForumList('moveTo', $thread['forum']);

$fields['btnEditThread'] = "<input type=\"submit\" name=\"actionedit\" value=\"".__("Edit")."\">";

echo "
	<script src=\"".resourceLink("js/threadtagging.js")."\"></script>
	<form action=\"".htmlentities(actionLink("editthread"))."\" method=\"post\">";
	
RenderTemplate('form_editthread', array(
	'fields' => $fields,
	'canRename' => $canRename,
	'canClose' => $canClose,
	'canStick' => $canStick,
	'canMove' => $canMove));
	
echo "
		<input type=\"hidden\" name=\"id\" value=\"$tid\">
		<input type=\"hidden\" name=\"key\" value=\"".$loguser['token']."\">
		<input type=\"hidden\" name=\"ref\" value=\"".htmlspecialchars($_SERVER['HTTP_REFERER'])."\">
	</form>";

?>
