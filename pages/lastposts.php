<?php
if (!defined('BLARG')) die();

$title = __("Last posts");
MakeCrumbs(array(actionLink("lastposts") => __("Last posts")));

$allowedforums = ForumsWithPermission('forum.viewforum');

$time = $_GET['time'];
if ($time != 'new') $time = (int)$time;
if (!$time) $time = 86400;
$show = $_GET['show'];
if ($show != 'threads' && $show != 'posts') $show = 'threads';

$from = (int)$_GET['from'];
$fparam = $from ? '&from='.$from : '';

$spans = array(3600=>__('1 hour'), 86400=>__('1 day'), 259200=>__('3 days'), 'new'=>__('New posts'));
$options = array();
foreach($spans as $span=>$desc)
{
	if ($span == $time)
		$options[] = $desc;
	else
		$options[] = actionLinkTag($desc, 'lastposts', '', 'time='.$span.'&show='.$show.$fparam);
}
$options2 = array();
$options2[] = ($show=='threads') ? __('List threads') : actionLinkTag(__('Show threads'), 'lastposts', '', 'time='.$time.'&show=threads'.$fparam);
$options2[] = ($show=='posts') ? __('Show posts') : actionLinkTag(__('Show posts'), 'lastposts', '', 'time='.$time.'&show=posts'.$fparam);

RenderTemplate('lastposts_options', array('timelinks' => $options, 'misclinks' => $options2));

$mindate = ($time=='new') ? ($loguserid ? 'IFNULL(tr.date,0)' : '{2}') : '{1}';
$total = FetchResult("SELECT COUNT(".($show=='threads'?'DISTINCT p.thread':'*').") FROM {posts} p LEFT JOIN {threads} t ON t.id=p.thread ".
	(($loguserid&&($time=='new'))?'LEFT JOIN {threadsread} tr ON tr.thread=p.thread AND tr.id={0}':'').
	" WHERE p.date>{$mindate} AND t.forum IN ({3c})", $loguserid, time()-$time, time()-900, $allowedforums);

if (!$total)
{
	Alert($time=='new' ? __('No unread posts.') : __('No posts have been made during this timespan.'), __('Notice'));
	return;
}

$perpage = ($show=='posts') ? $loguser['postsperpage'] : $loguser['threadsperpage'];
$pagelinks = PageLinks(actionLink("lastposts", '', "time={$time}&show={$show}&from="), $perpage, $from, $total);

if ($show == 'threads')
{
	$mindate = ($time=='new') ? ($loguserid ? 'IFNULL(tr.date,0)' : '{2}') : '{1}';
	$rThreads = Query("	SELECT
							t.*,
							f.(id,title),
							".($loguserid ? "tr.date readdate," : '')."
							su.(_userfields),
							lu.(_userfields)
						FROM
							{threads} t
							".($loguserid ? "LEFT JOIN {threadsread} tr ON tr.thread=t.id AND tr.id={0}" : '')."
							LEFT JOIN {forums} f ON f.id=t.forum
							LEFT JOIN {users} su ON su.id=t.user
							LEFT JOIN {users} lu ON lu.id=t.lastposter
						WHERE t.forum IN ({5c}) AND t.lastpostdate>{$mindate}
						ORDER BY t.lastpostdate DESC LIMIT {3u}, {4u}", $loguserid, time()-$time, time()-900, $from, $perpage, $allowedforums);

	makeThreadListing($rThreads, $pagelinks, false, true);
}
else
{
	$mindate = ($time=='new') ? ($loguserid ? 'IFNULL(tr.date,0)' : '{2}') : '{1}';
	$rPosts = Query("	SELECT
							p.*,
							pt.text, pt.revision, pt.user AS revuser, pt.date AS revdate,
							u.(_userfields), u.(rankset,title,picture,posts,postheader,signature,signsep,lastposttime,lastactivity,regdate,globalblock,fulllayout),
							ru.(_userfields),
							du.(_userfields),
							t.id thread, t.title threadname,
							f.id fid
						FROM
							{posts} p
							LEFT JOIN {posts_text} pt ON pt.pid = p.id AND pt.revision = p.currentrevision
							LEFT JOIN {users} u ON u.id = p.user
							LEFT JOIN {users} ru ON ru.id=pt.user
							LEFT JOIN {users} du ON du.id=p.deletedby
							LEFT JOIN {threads} t ON t.id=p.thread
							".(($loguserid&&($time=='new'))?'LEFT JOIN {threadsread} tr ON tr.thread=t.id AND tr.id={0}':'')."
							LEFT JOIN {forums} f ON f.id=t.forum
							LEFT JOIN {categories} c ON c.id=f.catid
						WHERE p.date>{$mindate} AND f.id IN ({5c})
						ORDER BY date DESC LIMIT {3u}, {4u}", $loguserid, time()-$time, time()-900, $from, $perpage, $allowedforums);
				
	RenderTemplate('pagelinks', array('pagelinks' => $pagelinks, 'position' => 'top'));
	
	while($post = Fetch($rPosts))
		MakePost($post, POST_NORMAL, array('threadlink'=>1, 'tid'=>$post['thread'], 'fid'=>$post['fid'], 'noreplylinks'=>1));
		
	RenderTemplate('pagelinks', array('pagelinks' => $pagelinks, 'position' => 'bottom'));
}

?>
