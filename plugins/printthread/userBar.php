<?php

global $rssBar, $rssWidth, $fid, $tid;
$snp = explode("/", $_SERVER['SCRIPT_NAME']);
$s = $snp[count($snp)-1];
if($s == "thread.php")
{
	$rssBar .= "<a href=\"printthread.php?id=".$tid."\"><img src=\"plugins/printthread/print.png\" alt=\"Print\" title=\"Printable view\" /></a>";
	$rssWidth += 19;
}

?>