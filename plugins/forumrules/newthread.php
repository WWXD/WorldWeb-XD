<?php
$title = __("New thread");

AssertForbidden("makeThread");

if(!$loguserid) //Not logged in?
	Kill(__("You must be logged in to post."));

if(isset($_POST['id']))
	$_GET['id'] = $_POST['id'];

if(!isset($_GET['id']))
	Kill(__("Forum ID unspecified."));

$fid = (int)$_GET['id'];

if($loguser['powerlevel'] < 0)
	Kill(__("You're banned."));

$rFora = Query("select * from {forums} where id={0}", $fid);
if(NumRows($rFora))
	$forum = Fetch($rFora);
else
	Kill(__("Unknown forum ID."));

if($forum['locked'])
	Kill(__("This forum is locked."));

if($forum['minpowerthread'] > $loguser['powerlevel'])
	Kill(__("You are not allowed to post threads in this forum."));

if(isset($_POST['text']) || isset($_GET['rulesread']) || $forum["rulespost"] == 0)
	include("pages/newthread.php");
else
{
	$OnlineUsersFid = $fid;

	$crumbs = new PipeMenu();
	makeForumCrumbs($crumbs, $forum);
	$crumbs->add(new PipeMenuTextEntry(__("New thread")));
	makeBreadcrumbs($crumbs);


	$rPosts = Query("
			SELECT
				pt.text
			FROM
				{posts} p
				LEFT JOIN {posts_text} pt ON pt.pid = p.id AND pt.revision = p.currentrevision
			WHERE p.id={0}", $forum["rulespost"]);
	$post = Fetch($rPosts);

	echo "<div class=\"faq outline margin\" style=\"width: 60%; overflow: auto; margin: auto;\">";
	echo "<h3>Please read the ", htmlspecialchars($forum["title"]), " forum rules before posting a new thread.</h3><br><br>";
	echo CleanUpPost($post["text"]);
	echo "<br><br><br><h3>", actionLinkTag("I've read the rules, continue.", "newthread", $_GET["id"], "rulesread=1"),"</h3>";
	echo "</div>";


}
