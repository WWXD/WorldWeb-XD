<?php
// favorites page
// forum.php copypasta
if (!defined('BLARG')) die();

if (!$loguserid)
	Kill(__("You must be logged in to use this feature."));

if ($_GET['action'] == "markasread")
{
	Query("	REPLACE INTO 
				{threadsread} (id,thread,date) 
			SELECT 
				{0}, t.id, {1} 
			FROM 
				{threads} t
				INNER JOIN {favorites} fav ON fav.user={0} AND fav.thread=t.id",
		$loguserid, time());

	die(header("Location: ".actionLink("board")));
}
else if ($_GET['action'] == 'add' || $_GET['action'] == 'remove')
{
	if ($_GET['token'] !== $loguser['token'])
		Kill(__('No.'));
	
	$tid = (int)$_GET['id'];
	$thread = Query("SELECT t.forum FROM {threads} t WHERE t.id={0}", $tid);
	if (!NumRows($thread))
		Kill(__("Invalid thread ID."));
	
	$thread = Fetch($thread);
	if (!HasPermission('forum.viewforum', $thread['forum']))
		Kill(__("Nice try, hacker kid, but no."));
	
	if ($_GET['action'] == 'add')
		Query("INSERT IGNORE INTO {favorites} (user,thread) VALUES ({0},{1})", $loguserid, $tid);
	else
		Query("DELETE FROM {favorites} WHERE user={0} AND thread={1}", $loguserid, $tid);
	
	die(header('Location: '.$_SERVER['HTTP_REFERER']));
}

$title = 'Favorites';

$links = array(actionLinkTag(__("Mark threads read"), 'favorites', 0, 'action=markasread'));

MakeCrumbs(array(actionLink('favorites') => 'Favorites'), $links);

$viewableforums = ForumsWithPermission('forum.viewforum');

$total = FetchResult("SELECT COUNT(*) FROM {threads} t INNER JOIN {favorites} fav ON fav.user={0} AND fav.thread=t.id WHERE t.forum IN ({1c})", $loguserid, $viewableforums);
$tpp = $loguser['threadsperpage'];
if(isset($_GET['from']))
	$from = (int)$_GET['from'];
else
	$from = 0;

if(!$tpp) $tpp = 50;

$rThreads = Query("	SELECT
						t.*,
						tr.date readdate,
						su.(_userfields),
						lu.(_userfields),
						f.(id,title)
					FROM
						{threads} t
						INNER JOIN {favorites} fav ON fav.user={0} AND fav.thread=t.id
						LEFT JOIN {threadsread} tr ON tr.thread=t.id AND tr.id={0}
						LEFT JOIN {users} su ON su.id=t.user
						LEFT JOIN {users} lu ON lu.id=t.lastposter
						LEFT JOIN {forums} f ON f.id=t.forum
					WHERE f.id IN ({3c})
					ORDER BY sticky DESC, lastpostdate DESC LIMIT {1u}, {2u}", 
					$loguserid, $from, $tpp, $viewableforums);

$numonpage = NumRows($rThreads);

$pagelinks = PageLinks(actionLink('favorites', '', 'from='), $tpp, $from, $total);

if(NumRows($rThreads))
{
	makeThreadListing($rThreads, $pagelinks, true, true);
} 
else
	Alert(__("You do not have any favorite threads."), __("Notice"));

?>
