<?php
if (!defined('BLARG')) die();

if (!$loguserGroup['root']) die(header('Location: /?page=404'));

/*$toignore = array();
$ignoreforums = Query("SELECT id FROM {forums} WHERE ignoreposts=1");
while ($ig = Fetch($ignoreforums))
	$toignore[] = $ig['id'];

if (empty($toignore)) return;*/

// find all the users who posted in those forums
$posters = Query("SELECT u.id FROM {users} u");
while ($poster = Fetch($posters))
{
	// update the post numbers of all the user's posts
	Query("SET @pnum=0");
	Query("UPDATE {posts} p SET num=(@pnum:=@pnum+1) WHERE p.user={0} ORDER BY date", $poster['id']);
	
	// update the user's postcount
	Query("UPDATE {users} u SET u.posts=(SELECT MAX(p.num) FROM {posts} p WHERE p.user=u.id) WHERE u.id={0}", $poster['id']);
}

?>