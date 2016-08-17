<?php
if (!defined('BLARG')) die();

$viewableforums = ForumsWithPermission('forum.viewforum');
	
$homepage = Settings::get('homepageText');

// timestamp => data
$lastActivity = array();
$maxitems = 10;

$lastposts = Query("	SELECT
							t.(title,forum,lastpostdate,lastpostid),
							u.(_userfields)
						FROM
							{threads} t
							LEFT JOIN {forums} f ON f.id=t.forum
							LEFT JOIN {users} u ON u.id=t.lastposter
						WHERE f.id IN ({0c}) AND f.offtopic=0
						ORDER BY t.lastpostdate DESC
						LIMIT {1u}", $viewableforums, $maxitems);
						
while ($lp = Fetch($lastposts))
{
	$user = getDataPrefix($lp, 'u_');
	$tags = ParseThreadTags($lp['t_title']);
	
	$fmtdate = relativedate($lp['t_lastpostdate']);
	$desc = UserLink($user).__(' posted in ').actionLinkTag($tags[0], 'post', $lp['t_lastpostid']);
	
	$lastActivity[$lp['t_lastpostdate']] = array('description' => $desc, 'formattedDate' => $fmtdate);
}

$bucket = 'lastactivity'; include(BOARD_ROOT.'lib/pluginloader.php');

krsort($lastActivity);
$lastActivity = array_slice($lastActivity, 0, $maxitems);

RenderTemplate('homepage', array('homepage' => $homepage, 'lastactivity' => $lastActivity));
	

$rFora = Query("select * from {forums} where id = {0}", Settings::get('newsForum'));
if(NumRows($rFora))
{
	$forum = Fetch($rFora);
	if(!HasPermission('forum.viewforum', $forum['id']))
		return;
} else
	return;

$fid = $forum['id'];

$total = $forum['numthreads'];

if(isset($_GET['from']))
	$from = (int)$_GET['from'];
else
	$from = 0;

$tpp = 5;

//echo '<br>';
$links = array('<a href="'.URL_ROOT.'rss.php">'.__('RSS feed').'</a>');
if (HasPermission('forum.postthreads', $forum['id']))
	$links[] = actionLinkTag(__('Post new'), 'newthread', $forum['id']);

MakeCrumbs(array(), $links);

$rThreads = Query("	SELECT 
						t.id, t.title, t.closed, t.replies, t.lastpostid,
						p.id pid, p.date,
						pt.text,
						su.(_userfields),
						lu.(_userfields)
					FROM 
						{threads} t
						LEFT JOIN {posts} p ON p.thread=t.id AND p.id=t.firstpostid
						LEFT JOIN {posts_text} pt ON pt.pid=p.id AND pt.revision=p.currentrevision
						LEFT JOIN {users} su ON su.id=t.user
						LEFT JOIN {users} lu ON lu.id=t.lastposter
					WHERE t.forum={0} AND p.deleted=0
					ORDER BY p.date DESC LIMIT {1u}, {2u}", $fid, $from, $tpp);

$numonpage = NumRows($rThreads);

$pagelinks = PageLinks(actionLink('home', '', 'from='), $tpp, $from, $total);

RenderTemplate('pagelinks', array('pagelinks' => $pagelinks, 'position' => 'top'));

while($thread = Fetch($rThreads))
{
	$pdata = array();
	
	$starter = getDataPrefix($thread, 'su_');
	$last = getDataPrefix($thread, 'lu_');

	$tags = ParseThreadTags($thread['title']);
	
	$pdata['title'] = $tags[0];
	$pdata['formattedDate'] = formatdate($thread['date']);
	$pdata['userlink'] = UserLink($starter);
	$pdata['text'] = CleanUpPost($thread['text'],$starter['name'], false, false);

	if (!$thread['replies'])
		$comments = 'No comments yet';
	else if ($thread['replies'] < 2)
		$comments = actionLinkTag('1 comment', 'post', $thread['lastpostid']).' (by '.UserLink($last).')';
	else
		$comments = actionLinkTag($thread['replies'].' comments', 'post', $thread['lastpostid']).' (last by '.UserLink($last).')';
	$pdata['comments'] = $comments;

	if ($thread['closed'])
		$newreply = __('Comment posting closed.');
	else if (!$loguserid)
		$newreply = actionLinkTag(__('Log in'), 'login').__(' to post a comment.');
	else
		$newreply = actionLinkTag(__("Post a comment"), "newreply", $thread['id']);
	$pdata['replylink'] = $newreply;
	
	$modlinks = array();
	if (($loguserid == $starter['id'] && HasPermission('user.editownposts')) || HasPermission('mod.editposts', $forum['id']))
		$modlinks['edit'] = actionLinkTag(__('Edit'), 'editpost', $thread['pid']);
	if (($loguserid == $starter['id'] && HasPermission('user.deleteownposts')) || HasPermission('mod.deleteposts', $forum['id']))
		$modlinks['delete'] = actionLinkTag(__('Delete'), 'editpost', $thread['pid'], 'delete=1&key='.$loguser['token']);

	RenderTemplate('newspost', array('post' => $pdata));
}

RenderTemplate('pagelinks', array('pagelinks' => $pagelinks, 'position' => 'bottom'));

?>