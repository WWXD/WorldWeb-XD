<?php

// Uploader support hub

$uploaddir = DATA_DIR."uploader";

// uploader settings

$goodfiles = explode(' ', Settings::pluginGet('uploaderWhitelist'));
$badfiles = array(	"html", "htm", "php", "php2", "php3", "php4", "php5", "php6", 
					"htaccess", "htpasswd", "mht", "js", "asp", "aspx", "cgi", "py", 
					"exe", "com", "bat", "pif", "cmd", "lnk", "wsh", "vbs", 
					"vbe", "jse", "wsf", "msc", "pl", "rb", "shtm", "shtml", 
					"stm", "htc");

$userquota = Settings::pluginGet('uploaderCap') * 1024 * 1024;
$maxSize = Settings::pluginGet('uploaderMaxFileSize') * 1024 * 1024;

$uploaddirs = array(0 => $uploaddir, $uploaddir.'/'.$loguserid, $uploaddir.'/attachments', $uploaddir.'/objectdb');


function uploaderCleanup()
{
	global $uploaddir;
	
	$timebeforedel = 604800; // one week
	$todelete = Query("SELECT physicalname, user, private FROM {uploader} WHERE deldate!=0 AND deldate<{0}", time()-$timebeforedel);
	while ($entry = Fetch($todelete))
	{
		if($entry['private'])
			@unlink($uploaddir."/".$entry['user']."/".$entry['physicalname']);
		else
			@unlink($uploaddir."/".$entry['physicalname']);
	}
	Query("DELETE FROM {uploader} WHERE deldate!=0 AND deldate<{0}", time()-$timebeforedel);
}

// cattype: 0=uploader file, 1=private file, 2=attachment, 3=objectdb
// cat: type=0: uploader category, type=1: nothing, type=2: post ID, type=3: object ID (except not-- object IDs aren't numeric)
function uploadFile($file, $cattype, $cat)
{
	global $loguserid, $uploaddirs, $goodfiles, $badfiles, $userquota, $maxSize;
	
	$targetdir = $uploaddirs[$cattype];
	$totalsize = foldersize($targetdir);
	
	$filedata = $_FILES[$file];

	$c = FetchResult("SELECT COUNT(*) FROM {uploader} WHERE filename={0} AND cattype={1} AND user={2} AND deldate=0", $filedata['name'], $cattype, $loguserid);
	if ($c > 0) 
		return "You already have a file with this name. Please delete the old copy before uploading a new one.";
		
	if($filedata['size'] == 0)
	{
		if($filedata['tmp_name'] == '')
			return 'No file given.';
		else
			return 'File is empty.';
	}
	
	if($filedata['size'] > $maxSize)
		return 'File is too large. Maximum size allowed is '.BytesToSize($maxSize).'.';

	$randomid = Shake();
	$pname = $randomid.'_'.Shake();
	
	$fname = $_FILES['newfile']['name'];
	$temp = $_FILES['newfile']['tmp_name'];
	$size = $_FILES['size']['size'];
	$parts = explode(".", $fname);
	$extension = end($parts);
	if($totalsize + $size > $quot)
		Alert(format(__("Uploading \"{0}\" would break the quota."), $fname));
	else if(in_array(strtolower($extension), $badfiles) || is_array($goodfiles) && !in_array(strtolower($extension), $goodfiles))
		return 'Forbidden file type.';
	else
	{
		$description = $_POST['description'];
		$big_descr = $cat['showindownloads'] ? $_POST['big_description'] : '';

		Query("insert into {uploader} (id, filename, description, big_description, date, user, private, category, deldate, physicalname) values ({7}, {0}, {1}, {6}, {2}, {3}, {4}, {5}, 0, {8})",
			$fname, $description, time(), $loguserid, $privateFlag, $_POST['cat'], $big_descr, $randomid, $pname);

		copy($temp, $targetdir."/".$pname);
		Report("[b]".$loguser['name']."[/] uploaded file \"[b]".$fname."[/]\"".($privateFlag ? " (privately)" : ""), $privateFlag);

		die(header("Location: ".actionLink("uploaderlist", "", "cat=".$_POST["cat"])));
	}
}

//From the PHP Manual User Comments
function foldersize($path)
{
	$total_size = 0;
	$files = scandir($path);
	$files = array_slice($files, 2);
	foreach($files as $t)
	{
		$size = filesize($path . "/" . $t);
		$total_size += $size;
	}
	return $total_size;
}

function getCategory($cat)
{
	if (!is_numeric($cat))
		Kill('Invalid category');

	if($cat >= 0)
	{
		$rCategory = Query("select * from {uploader_categories} where id={0}", $cat);
		if(NumRows($rCategory) == 0) Kill("Invalid category");
		$rcat = Fetch($rCategory);
	}
	else if($cat == -1)
		$rcat = array("id" => -1, "name" => "Private files", 'minpower' => 0);
	else if($cat == -2)
		$rcat = array("id" => -2, "name" => "All private files", 'minpower' => 99);
	else
		Kill('Invalid category');

	return $rcat;
}

?>