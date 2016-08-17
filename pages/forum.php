<?php
//  AcmlmBoard XD - Thread listing page
//  Access: all
if (!defined('BLARG')) die();

if(!isset($_GET['id']))
	Kill(__("Forum ID unspecified."));

$fid = (int)$_GET['id'];

if (!HasPermission('forum.viewforum', $fid))
	Kill(__('You may not access this forum.'));

$rFora = Query("select * from {forums} where id={0}", $fid);
if(NumRows($rFora))
	$forum = Fetch($rFora);
else
	Kill(__("Unknown forum ID."));
	
if ($forum['redirect'])
	die(header('Location: '.forumRedirectURL($forum['redirect'])));
	
if($loguserid)
{
	if($_GET['action'] == "markasread")
	{
		Query("REPLACE INTO {threadsread} (id,thread,date) SELECT {0}, {threads}.id, {1} FROM {threads} WHERE {threads}.forum={2}",
			$loguserid, time(), $fid);

		die(header("Location: ".actionLink("board", $forum['board'])));
	}
	
	$isIgnored = FetchResult("select count(*) from {ignoredforums} where uid={0} and fid={1}", $loguserid, $fid) == 1;
	if(isset($_GET['ignore']))
	{
		if(!$isIgnored)
			Query("insert into {ignoredforums} values ({0}, {1})", $loguserid, $fid);
		die(header("Location: ".$_SERVER['HTTP_REFERER']));
	}
	else if(isset($_GET['unignore']))
	{
		if($isIgnored)
			Query("delete from {ignoredforums} where uid={0} and fid={1}", $loguserid, $fid);
		die(header("Location: ".$_SERVER['HTTP_REFERER']));
	}
}

$title = $forum['title'];
$urlname = HasPermission('forum.viewforum', $fid, true) ? $title : '';


$links = array();
if($loguserid)
	$links[] = actionLinkTag(__("Mark forum read"), "forum", $fid, "action=markasread", $urlname);

if($loguserid)
{
	if($isIgnored)
		$links[] = actionLinkTag(__("Unignore forum"), "forum", $fid, "unignore", $urlname);
	else
		$links[] = actionLinkTag(__("Ignore forum"), "forum", $fid, "ignore", $urlname);

	if (HasPermission('forum.postthreads', $fid))
		$links[] = actionLinkTag(__("Post thread"), "newthread", $fid, '', $urlname);
}

$metaStuff['description'] = htmlspecialchars(strip_tags($forum['description']));
$metaStuff['tags'] = getKeywords(strip_tags($forum['title']));

$OnlineUsersFid = $fid;
MakeCrumbs(forumCrumbs($forum), $links);

makeAnncBar();

makeForumListing($fid);


$total = $forum['numthreads'];
$tpp = $loguser['threadsperpage'];
if(isset($_GET['from']))
	$from = (int)$_GET['from'];
else
	$from = 0;

if(!$tpp) $tpp = 50;

$rThreads = Query("	SELECT
						t.*,
						".($loguserid ? "tr.date readdate," : '')."
						su.(_userfields),
						lu.(_userfields)
					FROM
						{threads} t
						".($loguserid ? "LEFT JOIN {threadsread} tr ON tr.thread=t.id AND tr.id={3}" : '')."
						LEFT JOIN {users} su ON su.id=t.user
						LEFT JOIN {users} lu ON lu.id=t.lastposter
					WHERE forum={0}
					ORDER BY sticky DESC, lastpostdate DESC LIMIT {1u}, {2u}", $fid, $from, $tpp, $loguserid);

$pagelinks = PageLinks(actionLink("forum", $fid, "from=", $urlname), $tpp, $from, $total);
	
$ppp = $loguser['postsperpage'];
if(!$ppp) $ppp = 20;

if(NumRows($rThreads))
{
	makeThreadListing($rThreads, $pagelinks);
} 
else
	if(!HasPermission('forum.postthreads', $fid))
		Alert(__("You cannot start any threads here."), __("Empty forum"));
	elseif($loguserid)
		Alert(format(__("Would you like to {0}?"), actionLinkTag(__("post something"), "newthread", $fid)), __("Empty forum"));
	else
		Alert(format(__("{0} so you can post something."), actionLinkTag(__("Log in"), "login")), __("Empty forum"));

ForumJump();


function fj_forumBlock($fora, $catid, $selID, $indent)
{
	$ret = '';
	
	foreach ($fora[$catid] as $forum)
	{
		if ($forum['redirect'])
			$forumlink = forumRedirectURL($forum['redirect']);
		else
			$forumlink = actionLink('forum', $forum['id'], '', HasPermission('forum.viewforum',$forum['id'],true)?$forum['title']:'');
			
		$ret .=
'				<option value="'.htmlentities($forumlink)
	.'"'.($forum['id'] == $selID ? ' selected="selected"':'').'>'
	.str_repeat('&nbsp; &nbsp; ', $indent).htmlspecialchars($forum['title'])
	.'</option>
';
		if (!empty($fora[-$forum['id']]))
			$ret .= fj_forumBlock($fora, -$forum['id'], $selID, $indent+1);
	}
	
	return $ret;
}

function ForumJump()
{
	global $fid, $loguserid, $loguser, $forum;
	
	$viewableforums = ForumsWithPermission('forum.viewforum');
	$viewhidden = HasPermission('user.viewhiddenforums');
	
	$rCats = Query("SELECT id, name FROM {categories} WHERE board={0} ORDER BY corder, id", $forum['board']);
	$cats = array();
	while ($cat = Fetch($rCats))
		$cats[$cat['id']] = $cat['name'];

	$rFora = Query("	SELECT
							f.id, f.title, f.catid, f.redirect
						FROM
							{forums} f
						WHERE f.id IN ({0c})".(!$viewhidden ? " AND f.hidden=0" : '')."
						ORDER BY f.forder, f.id", $viewableforums);
						
	$fora = array();
	while($forum = Fetch($rFora))
		$fora[$forum['catid']][] = $forum;

	$theList = '';
	foreach ($cats as $cid=>$cname)
	{
		if (empty($fora[$cid]))
			continue;
			
		$theList .= 
'			<optgroup label="'.htmlspecialchars($cname).'">
'.fj_forumBlock($fora, $cid, $fid, 0).
'			</optgroup>
';
	}
	
	$theList = '<select onchange="document.location=this.options[this.selectedIndex].value;">'
		.($forum['board']?'<option value="'.actionLink('board').'">Back to main forums</option>':'')
		.$theList
		.'</select>';

	RenderTemplate('forumjump', array('forumlist' => $theList));
}

?>
