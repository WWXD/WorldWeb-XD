<?php
if (!defined('BLARG')) die();

$uid = (int)$_GET['id'];

$rUser = Query("select * from {users} where id={0}", $uid);
if(NumRows($rUser))
	$user = Fetch($rUser);
else
	Kill(__("Unknown user ID."));

$title = __("Thread list");

$uname = $user["name"];
if($user["displayname"])
	$uname = $user["displayname"];

MakeCrumbs(array(actionLink("profile", $uid, "", $user["name"]) => htmlspecialchars($uname), '' => __("List of threads")));

$viewableforums = ForumsWithPermission('forum.viewforum');

$total = FetchResult("SELECT
						count(*)
					FROM
						{threads} t
					WHERE t.user={0} AND t.forum IN ({1c})", $uid, $viewableforums);

$tpp = $loguser['threadsperpage'];
if(isset($_GET['from']))
	$from = (int)$_GET['from'];
else
	$from = 0;

if(!$tpp) $tpp = 50;

$rThreads = Query("	SELECT
						t.*,
						f.(title, id),
						".($loguserid ? "tr.date readdate," : '')."
						su.(_userfields),
						lu.(_userfields)
					FROM
						{threads} t
						".($loguserid ? "LEFT JOIN {threadsread} tr ON tr.thread=t.id AND tr.id={4}" : '')."
						LEFT JOIN {users} su ON su.id=t.user
						LEFT JOIN {users} lu ON lu.id=t.lastposter
						LEFT JOIN {forums} f ON f.id=t.forum
					WHERE t.user={0} AND f.id IN ({5c})
					ORDER BY lastpostdate DESC LIMIT {2u}, {3u}", $uid, null, $from, $tpp, $loguserid, $viewableforums);

$pagelinks = PageLinks(actionLink("listthreads", $uid, "from=", $user['name']), $tpp, $from, $total);

$ppp = $loguser['postsperpage'];
if(!$ppp) $ppp = 20;

if(NumRows($rThreads))
{
	makeThreadListing($rThreads, $pagelinks, false, true);
}
else
	Alert(__("No threads found."), __("Notice"));

?>