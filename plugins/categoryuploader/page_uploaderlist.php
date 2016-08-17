<?php

include('lib.php');

$title = __("Uploader");

$rootdir = DATA_DIR."uploader";

if($uploaderWhitelist)
	$goodfiles = explode(" ", $uploaderWhitelist);

$badfiles = array("html", "htm", "php", "php2", "php3", "php4", "php5", "php6", "htaccess", "htpasswd", "mht", "js", "asp", "aspx", "cgi", "py", "exe", "com", "bat", "pif", "cmd", "lnk", "wsh", "vbs", "vbe", "jse", "wsf", "msc", "pl", "rb", "shtm", "shtml", "stm", "htc");

function listCategory($cat)
{
	global $loguser, $loguserid, $rootdir, $userSelectUsers, URL_ROOT;

	if(isset($_GET['sort']) && $_GET['sort'] == "filename" || $_GET['sort'] == "date")
		$skey = $_GET['sort'];
	else
		$skey = "date";

	$sortOptions = "<div class=\"margin smallFonts\">".__("Sort order").": <ul class=\"pipemenu\">";
	$sortOptions .= ($skey == "filename")
			?"<li>".__("Name")."</li>"
			:actionLinkTagItem(__("Name"), "uploaderlist", "", "cat=${_GET["cat"]}&sort=filename");
	$sortOptions .= ($skey == "date")
			?"<li>".__("Date")."</li>"
			:actionLinkTagItem(__("Date"), "uploaderlist", "", "cat=${_GET["cat"]}&sort=date");
	$sortOptions .= "</ul></div>";
	$sdir = ($skey == "date") ? " desc" : " asc";


	print $sortOptions;

	if($cat == -1)
		$condition = "up.user = ".$loguserid." and up.private = 1";
	else if($cat == -2 && HasPermission('uploader.viewprivate'))
		$condition = "up.private = 1";
	else
		$condition = "up.private = 0 and up.category = {0}";
	
	if (!HasPermission('uploader.deletefiles'))
		$condition .= ' AND up.deldate=0';

	$errormsg = __("The category is empty.");
	if($cat < 0)
		$errormsg = __("You have no private files.");

	$entries = Query("SELECT
			up.*,
			u.(_userfields)
			FROM {uploader} up
			LEFT JOIN {users} u on up.user = u.id
			WHERE $condition
			ORDER BY ".$skey.$sdir, $cat);

	$checkbox = "";
	if($loguserid)
	{
		$checkbox = "<input type=\"checkbox\" id=\"ca\" onchange=\"checkAll();\" />";
		$checkbox = "<th style=\"width: 22px;\">$checkbox</th>";
	}

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
		<table class=\"outline margin\">
			<tr class=\"header0\">
				<th colspan=\"7\">".__("Files")."</th>
			</tr>

		";

		print 	"
			<tr class=\"header1\">
				$checkbox
				<th>
					".__("File")."
				</th>
				<th>
					".__("Description")."
				</th>
				<th>
					".__("Size")."
				</th>
				<th>
					".__("Uploader")."
				</th>
				<th>
					".__("Downloads")."
				</th>
			</tr>
		";

		while($entry = Fetch($entries))
		{
			$delete = "";
			$multidel = "";
			$filename = htmlspecialchars($entry['filename']);
			if($loguserid)
				$multidel = "<td><input type=\"checkbox\" name=\"delete[".$entry['id']."]\" disabled=\"disabled\" /></td>";
			if (HasPermission('uploader.deletefiles') && $entry['deldate'] != 0)
			{
				$delete = "&nbsp;<sup title=\"Restore\">"
					.actionLinkTag("&#x21B6;", "uploader", "", "action=restore&fid=".$entry['id'].'&cat='.$cat.'&token='.$loguser['token'])
					."</sup>";
				$filename = "<span style=\"color:#f44;\">[DELETED] {$filename}</span>";
			}
			else if($loguserid == $entry['user'] || HasPermission('uploader.deletefiles'))
			{
				$delete = "&nbsp;<sup title=\"Delete\">"
					.actionLinkTag("&#x2718;", "uploader", "", "action=delete&fid=".$entry['id'].'&cat='.$cat.'&token='.$loguser['token'])
					."</sup>";
				$multidel = "<td><input type=\"checkbox\" name=\"del[".$entry['id']."]\" /></td>";
			}
			$cellClass = ($cellClass+1) % 2;

			$filepath = $rootdir."/".$entry['physicalname'];
			if($entry['private'])
				$filepath = $rootdir."/".$entry['user']."/".$entry['physicalname'];

			print format(
			"
			<tr class=\"cell{0}\">
				{7}
				<td>
					<a href=\"".URL_ROOT."get.php?id={1}\">{2}</a>{3}
				</td>
				<td>
					{4}
				</td>
				<td>
					{5}
				</td>
				<td>
					{6}
				</td>
				<td>
					{8}
				</td>
			</tr>
			",	$cellClass, $entry['id'], $filename, $delete, htmlspecialchars($entry['description']),
				BytesToSize(@filesize($filepath)), UserLink(getDataPrefix($entry, "u_")), $multidel, $entry["downloads"]);
		}


		if($loguserid)
		{
			$entries = Query("select * from {uploader_categories} order by ord");
			$movelist = "";

			while($entry = Fetch($entries))
			{
				$movelist .= "<option value='${entry["id"]}'>${entry["name"]}</option>";
			}
			$movelist .= "<option value='-1'>Private files</option>";
			$movelist = "<select name='destcat' size='1'>$movelist</select>";

			print format("
				<tr class=\"header1\">
					<th style=\"text-align: left!important;\" colspan=\"6\">
						<input type=\"hidden\" id='actionfield' name=\"action\" value=\"multidel\" />
						<a href=\"javascript:void();\" onclick=\"document.getElementById('actionfield').value = 'multidel'; document.forms[1].submit();\">".__("Delete checked")."</a> |
						<a href=\"javascript:void();\" onclick=\"document.getElementById('actionfield').value = 'multimove'; document.forms[1].submit();\">".__("Move checked to")."</a> $movelist
					</th>
				</tr>");
		}
			print "</table>";
	}
}


$cat = getCategory($_GET["cat"]);
$links = actionLinkTag("Upload file", "uploader", "", "action=uploadform&cat=".$_GET["cat"]);

if($_GET["cat"] == -2)
	$links = "";
if($isBot)
	$links = "";
if(!$loguserid)
	$links = "";
if ($cat['minpower'] && !HasPermission('uploader.uploadrestricted'))
	$links = '';

MakeCrumbs(array(
				actionLink("uploader") => "Uploader",
				actionLink("uploaderlist", "", "cat=".$cat["id"]) => $cat["name"],
				), $links);

print "<form method=\"post\" action=\"".htmlentities(actionLink("uploader", "", "cat=${_GET["cat"]}"))."\">";
listCategory($_GET["cat"]);
print '<input type="hidden" name="token" value="'.$loguser['token'].'" />';
print "</form>";

?>
