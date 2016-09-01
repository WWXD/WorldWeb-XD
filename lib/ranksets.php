<?php

function loadRanksets()
{
	global $ranksetData, $ranksetNames;
	
	if(isset($ranksetNames)) return;
	
	$ranksetData = array();
	$ranksetNames = array();

	$dir = "ranksets/";

    if (is_dir($dir)) {
        if ($dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                if(filetype($dir . $file) != "dir") continue;
                if($file == ".." || $file == ".") continue;
                $jsonfile = $dir.$file."/rankset.json";
                if(file_exists($jsonfile)) {
                    switch (json_last_error()) {
                    case JSON_ERROR_NONE:
                        break;
                    case JSON_ERROR_DEPTH:
                        echo ' - Maximum stack depth exceeded';
                        break;
                    case JSON_ERROR_STATE_MISMATCH:
                        echo ' - Underflow or the modes mismatch';
                        break;
                    case JSON_ERROR_CTRL_CHAR:
                        echo ' - Unexpected control character found';
                        break;
                    case JSON_ERROR_SYNTAX:
                        echo ' - Syntax error, malformed JSON';
                        break;
                    case JSON_ERROR_UTF8:
                        echo ' - Malformed UTF-8 characters, possibly incorrectly encoded';
                        break;
                    default:
                        echo ' - Unknown JSON error';
                        break;
                    }
                    $ranksetNames[$file] = $file;
                    $ranksetData[$file] = array();
                    $data = json_decode(file_get_contents($jsonfile, FILE_USE_INCLUDE_PATH), true);
                    foreach($data as $text => $d) {
                        $num = $d["num"];
                        $image = $d["image"];
                        array_push($ranksetData[$file],array("num" => $num, "image" => $image, "text" => $text));
                    }
                }
                else if(file_exists($dir.$file."/rankset.php"))
                    include($dir.$file."/rankset.php");
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
