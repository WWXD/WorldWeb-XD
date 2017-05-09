<?php
//  WorldWeb XD - Report/content mismatch fixing utility
//  Access: Owner
if (!defined('BLARG')) die();

if(!$loguser['root'])
	Kill(__("Owner only, please."));

MakeCrumbs([actionLink("admin") => __("Admin"), actionLink("recalc") => __("Recalculate statistics")]);

function startFix() {
	global $fixtime;
	$fixtime = usectime();
}

function reportFix($what, $aff = -1) {
	global $fixtime;

	if($aff = -1)
		$aff = affectedRows();
	echo $what, " ", format(__("{0} rows affected."), $aff), " time: ", sprintf('%1.3f', usectime()-$fixtime), "<br />";
}

$debugMode = false;

startFix();
query("UPDATE {users} u SET posts =
			(SELECT COUNT(*) FROM {posts} p WHERE p.user = u.id)
		");
reportFix(__("Counting user's posts&hellip;"));

startFix();
query("UPDATE {threads} t SET replies =
			(SELECT COUNT(*) FROM {posts} p WHERE p.thread = t.id) - 1
		");
reportFix(__("Counting thread replies&hellip;"));

startFix();
query("UPDATE {forums} f SET numthreads =
			(SELECT COUNT(*) FROM {threads} t WHERE t.forum = f.id)
		");
reportFix(__("Counting forum threads&hellip;"));

startFix();
query("UPDATE {forums} f SET numposts =
			(SELECT SUM(replies+1) FROM {threads} t WHERE t.forum = f.id)
		");
reportFix(__("Counting forum posts&hellip;"));

startFix();
$aff = 0;
$rForum = Query("select * from {forums}");
while($forum = Fetch($rForum)) {
	$rThread = Query("select * from {threads} where forum = {0} order by lastpostdate desc", $forum['id']);
	$first = 1;
	while($thread = Fetch($rThread)) {
		$lastPost = Fetch(Query("select * from {posts} where thread = {0} order by date desc limit 0,1", $thread['id']));
		Query("update {threads} set lastpostid = {0}, lastposter = {1}, lastpostdate = {2} where id = {3}", (int)$lastPost['id'], (int)$lastPost['user'], (int)$lastPost['date'], $thread['id']);
		$aff += affectedRows();
		if($first) {
			Query("update {forums} set lastpostid = {0}, lastpostuser = {1}, lastpostdate = {2} where id = {3}", (int)$lastPost['id'], (int)$lastPost['user'], (int)$lastPost['date'], $forum['id']);
			$aff += affectedRows();
		}
		$first = 0;
	}
}
reportFix(__("Updating threads last posts&hellip;"));

$bucket = "recalc"; include(BOARD_ROOT."lib/pluginloader.php");
echo "<br />All done!<br />";

