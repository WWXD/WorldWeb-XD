<?php

$snp = explode("/", $_SERVER['SCRIPT_NAME']);
$s = $snp[count($snp)-1];
if($s == "index.php")
{
	write("
	<link rel=\"alternate\" type=\"application/rss+xml\" title=\"RSS feed\" href=\"rss2.php\" />
");
}
else if($s == "forum.php")
{
	write("
	<link rel=\"alternate\" type=\"application/rss+xml\" title=\"RSS feed\" href=\"rss2.php\" />
	<link rel=\"alternate\" type=\"application/rss+xml\" title=\"RSS feed (forum)\" href=\"rss2.php?forum={0}\" />
", $_GET['id']);
}
else if($s == "thread.php")
{
	write("
	<link rel=\"alternate\" type=\"application/rss+xml\" title=\"RSS feed\" href=\"rss2.php\" />
	<link rel=\"alternate\" type=\"application/rss+xml\" title=\"RSS feed (thread)\" href=\"rss2.php?thread={0}\" />
", $_GET['id']);
}

?>