<?php
if (!defined('BLARG')) die();

$board = $_GET['id'];
if (!$board) $board = '';
if (!isset($forumBoards[$board])) $board = '';

if($loguserid && isset($_GET['action']) && $_GET['action'] == "markallread")
{
	Query("REPLACE INTO {threadsread} (id,thread,date) SELECT {0}, t.id, {1} FROM {threads} t".($board!='' ? ' LEFT JOIN {forums} f ON f.id=t.forum WHERE f.board={2}' : ''), 
		$loguserid, time(), $board);
		
	die(header("Location: ".actionLink("board", $board)));
}

$links = array();
if($loguserid)
	$links[] = actionLinkTag(__("Mark all forums read"), "board", $board, "action=markallread");

MakeCrumbs(forumCrumbs(array('board' => $board)), $links);
makeAnncBar();
makeForumListing(0, $board);

?>
