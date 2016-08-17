<?php

$snp = explode("/", $_SERVER['SCRIPT_NAME']);
$s = $snp[count($snp)-1];
if($s == "index.php")
{
	$rssBar .= "<a href=\"rss2.php\"><img src=\"plugins/rssfeeds/feed.png\" alt=\"RSS Feed\" title=\"RSS Feed\" /></a>";
	$rssWidth += 19;
}
else if($s == "forum.php")
{
	$rssBar .= "<a href=\"rss2.php?forum=".$fid."\"><img src=\"plugins/rssfeeds/feed.png\" alt=\"RSS Feed\" title=\"RSS Feed for this forum\" /></a>";
	$rssWidth += 19;
}
else if($s == "thread.php")
{
	$rssBar .= "<a href=\"rss2.php?thread=".$tid."\"><img src=\"plugins/rssfeeds/feed.png\" alt=\"RSS Feed\" title=\"RSS Feed for this thread\" /></a>";
	$rssWidth += 19;
}

?>