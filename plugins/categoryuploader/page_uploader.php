<?php

include('lib.php');

$title = __("Uploader");

$rootdir = DATA_DIR."uploader";

if($uploaderWhitelist)
	$goodfiles = explode(" ", Settings::pluginGet('uploaderWhitelist'));

$badfiles = array("html", "htm", "php", "php2", "php3", "php4", "php5", "php6", "htaccess", "htpasswd", "mht", "js", "asp", "aspx", "cgi", "py", "exe", "com", "bat", "pif", "cmd", "lnk", "wsh", "vbs", "vbe", "jse", "wsf", "msc", "pl", "rb", "shtm", "shtml", "stm", "htc");

if(isset($_POST['action']))
	$_GET['action'] = $_POST['action'];
if(isset($_POST['fid']))
	$_GET['fid'] = $_POST['fid'];
	
if ($_GET['action'])
{
	if (!$loguserid)
		Kill('You must be logged in to use this feature.');
		
	if ($_GET['action'] != 'uploadform' && $_REQUEST['token'] !== $loguser['token'])
		Kill('No.');
}

$quota = Settings::pluginGet('uploaderCap') * 1024 * 1024;
$pQuota = Settings::pluginGet('personalCap') * 1024 * 1024;
$totalsize = foldersize($rootdir);

$maxSizeMult = Settings::pluginGet('uploaderMaxFileSize') * 1024 * 1024;

if($_GET['action'] == "uploadform")
{
	$cat = getCategory($_GET["cat"]);
	if (!is_numeric($_GET["cat"]))
		Kill('Invalid category');
		
	CheckPermission('uploader.uploadfiles');

	$cat = getCategory($_GET["cat"]);
	if ($cat['minpower'])
		CheckPermission('uploader.uploadrestricted');

	MakeCrumbs(array(
					actionLink("uploader") => "Uploader",
					actionLink("uploaderlist", "", "cat=".$cat["id"]) => $cat["name"],
					actionLink("uploader", "", "action=uploadform&cat=".$cat["id"]) => "Upload new file"), $links);

	print format(
	"
	<script type=\"text/javascript\">
		window.addEventListener(\"load\", function() { hookUploadCheck(\"newfile\", 1, {1}) }, false);
	</script>
	<form action=\"".htmlentities(actionLink("uploader"))."\" method=\"post\" enctype=\"multipart/form-data\">
		<input type='hidden' name='cat' value='${_GET["cat"]}'>
		<table class=\"outline margin\">
			<tr class=\"header0\">
				<th colspan=\"4\">".__("Upload")."</th>
			</tr>
			<tr class=\"cell0\">
				<td>File</td><td>
					<input type=\"file\" id=\"newfile\" name=\"newfile\" style=\"width: 80%;\" />
				</td>
			</tr>
			<tr class=\"cell1\">
				<td>{3}</td><td>
					<input type=\"text\" name=\"description\" style=\"width: 80%;\" />
				</td>
			</tr>
			".(!$cat['showindownloads']?'':
			"<tr class=\"cell1\">
				<td>Description</td><td>
					<textarea name=\"big_description\" style=\"width: 80%; height: 8em;\"></textarea>
				</td>
			</tr>
			")."
			<tr class=\"cell0\">
				<td></td><td>
					<input type=\"submit\" id=\"submit\" name=\"action\" value=\"".__("Upload")."\" disabled=\"disabled\" />
				</td>
			</tr>
			<tr class=\"cell1 smallFonts\">
				<td colspan=\"3\">
					".__("The maximum upload size is {0} per file. You can upload the following types: {2}.")."
					<div id=\"sizeWarning\" style=\"display: none; font-weight: bold\">".__("File is too large.")."</div>
					<div id=\"typeWarning\" style=\"display: none; font-weight: bold\">".__("File is not an allowed type.")."</div>
				</td>
			</tr>
		</table>
		<input type=\"hidden\" name=\"token\" value=\"{$loguser['token']}\" />
	</form>
	", BytesToSize($maxSizeMult), $maxSizeMult, Settings::pluginGet('uploaderWhitelist'),
	$cat['showindownloads']?'Name':'Description');
}

else if($_GET['action'] == __("Upload"))
{
	CheckPermission('uploader.uploadfiles');

	$cat = getCategory($_POST["cat"]);
	if ($cat['minpower'])
		CheckPermission('uploader.uploadrestricted');
	$targetdir = $rootdir;
	$quot = $quota;
	$privateFlag = 0;
	if($_POST['cat'] == -1)
	{
		$quot = $pQuota;
		$targetdir = $rootdir."/".$loguserid;
		$privateFlag = 1;
	}
	$totalsize = foldersize($targetdir);

	$c = FetchResult("SELECT COUNT(*) FROM {uploader} WHERE filename={0} AND deldate=0", $_FILES['newfile']['name']);
	if ($c > 0) Kill("The file '{$_FILES['newfile']['name']}' already exists. Please delete the old copy before uploading a new one.");
		
	if($_FILES['newfile']['size'] == 0)
	{
		if($_FILES['newfile']['tmp_name'] == "")
			Alert(__("No file given."));
		else
			Alert(__("File is empty."));
	}
	else if($_FILES['newfile']['size'] > Settings::pluginGet('uploaderMaxFileSize') * 1024 * 1024)
	{
		Alert(format(__("File is too large. Maximum size is {0}."), BytesToSize(Settings::pluginGet('uploaderMaxFileSize') * 1024 * 1024)));
	}
	else
	{
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
		{
			Alert(__("Forbidden file type."));
		}
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
}
else if($_GET['action'] == "multidel" && $_POST['del']) //several files
{
	$deleted = array_keys($_POST['del']);
	$powercheck = HasPermission('uploader.deletefiles') ? '' : (HasPermission('uploader.deleteownfiles') ? 'AND user={2}' : 'AND 0=1');
	Query("UPDATE {uploader} SET deldate={0} WHERE id IN ({1c}) AND deldate=0 {$powercheck}", time(), $deleted, $loguserid);
	Report("[b]".$loguser['name']."[/] deleted files: ".implode(', ',$deleted), 1); // this sucks ass
	die(header("Location: ".actionLink("uploaderlist", "", "cat=".$_GET["cat"])));
}
else if($_GET['action'] == "multimove" && $_POST['del']) //several files
{
	$moved = array_keys($_POST['del']);
	$newcat = $_POST['destcat'];
	$cat = getCategory($newcat);
	if ($cat['minpower'])
		CheckPermission('uploader.uploadrestricted');

	$powercheck = HasPermission('uploader.movefiles') ? '' : (HasPermission('uploader.moveownfiles') ? 'AND user={2}' : 'AND 0=1');
	Query("UPDATE {uploader} SET category={0}, private={3} WHERE id IN ({1c}) AND deldate=0 {$powercheck}", $newcat, $moved, $loguserid, ($newcat==-1)?1:0);
	Report("[b]".$loguser['name']."[/] moved files to cat#{$newcat}: ".implode(', ',$moved), 1); // this sucks ass
	die(header("Location: ".actionLink("uploaderlist", "", "cat=".$_GET["cat"])));
}

else if($_GET['action'] == "delete") //single file
{
	$fid = $_GET['fid'];

	if(HasPermission('uploader.deletefiles'))
		$check = FetchResult("select count(*) from {uploader} where id = {0}", $fid);
	else
	{
		CheckPermission('uploader.deleteownfiles');
		$check = FetchResult("select count(*) from {uploader} where user = {0} and id = {1} AND deldate=0", $loguserid, $fid);
	}

	if($check)
	{
		$fn = FetchResult("SELECT filename FROM {uploader} WHERE id={0}", $fid);
		Query("UPDATE {uploader} SET deldate={0} WHERE id={1}", time(), $fid);
		Report("[b]".$loguser['name']."[/] deleted \"[b]".$fn."[/]\" ({$fid}).", 1);
		die(header("Location: ".actionLink("uploaderlist", "", "cat=".$_GET["cat"])));
	}
	else
		Alert(__("No such file or not yours to mess with."));
}
else if ($_GET['action'] == 'restore' && HasPermission('uploader.deletefiles'))
{
	$fid = $_GET['fid'];
	$check = FetchResult("select count(*) from {uploader} where id = {0}", $fid);
	if($check)
	{
		Query("UPDATE {uploader} SET deldate=0 WHERE id={0}", $fid);
		Report("[b]".$loguser['name']."[/] restored \"[b]".$entry['filename']."[/]\" ({$fid}).", 1);
		die(header("Location: ".actionLink("uploaderlist", "", "cat=".$_GET["cat"])));
	}
	else
		Alert(__("No such file."));
}
else
{
	MakeCrumbs(array(
					actionLink("uploader") => "Uploader"), $links);

	$errormsg = __("No categories found.");
	$entries = Query("select * from {uploader_categories} order by ord");

	if(NumRows($entries) == 0)
	{
		print "
		<table class=\"outline margin\">
			<tr class=\"header0\">



				<th colspan=\"7\">".__("Files")."</th>
			</tr>
			<tr class=\"cell1\">
				<td colspan=\"4\">
					".$errormsg."
				</td>
			</tr>
		</table>
		";
	}
	else
	{
		print
		"
		<table class=\"outline margin width100\">
			<tr class=\"header0\">
				<th colspan=\"7\">".__("Categories")."</th>
			</tr>
		";

		$cellClass = 0;

		while($entry = Fetch($entries))
		{
			$filecount = FetchResult("select count(*) from {uploader} where category = {0} AND deldate=0", $entry['id']);

			print "<tr class=\"cell$cellClass\"><td>";
			print actionLinkTag($entry['name'], "uploaderlist", "", "cat=".$entry['id']);
			print "<br />";
			print $entry['description'];
			print "<br />";
			print Plural($filecount, 'file');
			if (HasPermission('uploader.deletefiles'))
			{
				$ndel = FetchResult("SELECT COUNT(*) FROM {uploader} WHERE category={0} AND deldate!=0", $entry['id']);
				if ($ndel > 0) print " (and $ndel deleted)";
			}
			print ".<br />";
			print "</td></tr>";
			$cellClass = ($cellClass+1) % 2;
		}

		if($loguserid)
		{
			$filecount = FetchResult("select count(*) from {uploader} u where u.user = {0} and u.private = 1 AND u.deldate=0", $loguserid);

			print "<tr class=\"cell$cellClass\"><td>";
			print actionLinkTag("Private files", "uploaderlist", "", "cat=-1");
			print "<br />";
			print "Only for you.";
			print "<br />";
			print Plural($filecount, 'file');
			if (HasPermission('uploader.deletefiles'))
			{
				$ndel = FetchResult("select count(*) from {uploader} u where u.user = {0} and u.private = 1 AND u.deldate!=0", $loguserid);
				if ($ndel > 0) print " (and $ndel deleted)";
			}
			print ".<br />";
			print "</td></tr>";

			$cellClass = ($cellClass+1) % 2;

			if(HasPermission('uploader.viewprivate'))
			{
				$filecount = FetchResult("select count(*) from {uploader} u where u.private = 1 AND u.deldate=0");

				print "<tr class=\"cell$cellClass\"><td>";
				print actionLinkTag("All private files", "uploaderlist", "", "cat=-2");
				print "<br />";
				print Plural($filecount, 'file');
				if (HasPermission('uploader.deletefiles'))
				{
					$ndel = FetchResult("select count(*) from {uploader} u where u.private = 1 AND u.deldate!=0");
					if ($ndel > 0) print " (and $ndel deleted)";
				}
				print ".<br />";
				print "</td></tr>";
			}
		}
		print "</table>";
	}
}



?>
