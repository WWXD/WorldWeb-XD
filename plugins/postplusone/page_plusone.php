<?php
$ajaxPage = true;

if($_GET["key"] != $loguser["token"])
	die("Nope!");

CheckPermission('user.voteposts');

$pid = (int)$_GET["id"];

$post = Fetch(Query("SELECT * FROM {posts} WHERE id = {0}", $pid));
if(!$post)
	die("Unknown post");
if($post["user"] == $loguserid)
	die("Nope!");

$thread = Fetch(Query("SELECT * FROM {threads} WHERE id = {0}", $post["thread"]));
if(!$thread)
	die("Unknown thread");

if (!HasPermission('forum.viewforum', $thread['forum']))
	die('Nice try hacker kid, but no.');
if($thread["closed"])
	die(__("Thread is closed"));

$vote = Fetch(Query("SELECT * FROM {postplusones} WHERE post = {0} AND user = {1}", $pid, $loguserid));
if(!$vote)
{
	Query("UPDATE {posts} SET postplusones = postplusones+1 WHERE id = {0} LIMIT 1", $pid);
	Query("UPDATE {users} SET postplusones = postplusones+1 WHERE id = {0} LIMIT 1", $post["user"]);
	Query("UPDATE {users} SET postplusonesgiven = postplusonesgiven+1 WHERE id = {0} LIMIT 1", $loguserid);
	Query("INSERT INTO {postplusones} (user, post) VALUES ({0}, {1})", $loguserid, $pid);
	$post["postplusones"]++;
}
echo formatPlusOnes($post["postplusones"]);


