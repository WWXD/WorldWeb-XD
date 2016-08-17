<?php
if (!defined('BLARG')) die();

function loadRanksets()
{
	global $ranksetData, $ranksetNames;
	
	if(isset($ranksetNames)) return;
	
	$ranksetData = array();
	$ranksetNames = array();

	$dir = __DIR__."/../ranksets/";

	if (is_dir($dir))
	{
		if ($dh = opendir($dir))
		{
		    while (($file = readdir($dh)) !== false)
		    {
		        if(filetype($dir . $file) != "dir") continue;
		        if($file == ".." || $file == ".") continue;
		        $infofile = $dir.$file."/rankset.php";

		        if(file_exists($infofile))
		        	include($infofile);
		    }
		    closedir($dh);
		}
	}
}

function getRankHtml($rankset, $rank)
{
	$text = htmlspecialchars($rank["text"]);
	$img = '';
	if ($rank['image'])
	{
		$img = htmlspecialchars(resourceLink("ranksets/".$rankset."/".$rank["image"]));
		$img = "<img src=\"$img\" alt=\"\" /><br>";
	}
	return $img.$text;
}

function getRank($rankset, $posts)
{
	global $ranksetData;
	if(!$rankset) return "";
	if(!isset($ranksetData)) loadRanksets(); 

	$thisSet = $ranksetData[$rankset];
	if(!is_array($thisSet)) return "";
	$ret = "";
	foreach($thisSet as $row)
	{
		if($row["num"] > $posts)
			break;
		$ret = $row;
	}
	
	if(!$ret) return "";
	return getRankHtml($rankset, $ret);
}

function getToNextRank($rankset, $posts)
{
	global $ranksetData;
	if(!$rankset) return "";
	if(!isset($ranksetData)) loadRanksets(); 

	$thisSet = $ranksetData[$rankset];
	if(!is_array($thisSet)) return "";
	$ret = "";
	foreach($thisSet as $row)
	{
		$ret = $row["num"] - $posts;
		if($row["num"] > $posts)
			return $ret;
	}
}
