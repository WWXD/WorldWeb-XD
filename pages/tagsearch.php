<?php
if (!defined('BLARG')) die();

$viewableforums = ForumsWithPermission('forum.viewforum');

$tag = $_GET['tag'];
$tagcode = '"['.$tag.']"';
$forum = $_GET['fid'];

$cond = "WHERE MATCH (t.title) AGAINST ({0} IN BOOLEAN MODE)";

if($forum)
	$cond .= " AND t.forum = {1}";

$total = Fetch(Query("SELECT count(*) from threads t $cond AND t.forum IN ({2c})", $tag, $forum, $viewableforums));
$total = $total[0];

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
						threads t
						".($loguserid ? "LEFT JOIN threadsread tr ON tr.thread=t.id AND tr.id={2}" : '')."
						LEFT JOIN users su ON su.id=t.user
						LEFT JOIN users lu ON lu.id=t.lastposter
						LEFT JOIN forums f ON f.id=t.forum
					$cond AND f.id IN ({5c})
					ORDER BY sticky DESC, lastpostdate DESC LIMIT {3u}, {4u}",
					$tagcode, $forum, $loguserid, $from, $tpp, $viewableforums);

$pagelinks = PageLinks(actionLink("tagsearch", "", "tag=$tag&fid=$forum&from="), $tpp, $from, $total);

if(NumRows($rThreads))
{
	makeThreadListing($rThreads, $pagelinks, false, !$forum);
} 
else
	Alert(format(__("Tag {0} was not found in any thread."), htmlspecialchars($tag)), __("No threads found."));

?>