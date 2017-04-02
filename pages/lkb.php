<?php

CheckPermission('admin.viewips');

$title = "Last known browsers";

$isMod = HasPermission('admin.viewips');
$sort = "id asc";
$ual = "";
if(isset($_GET['byua']))
{
	$sort = "lastknownbrowser asc";
	$ual .= "byua&amp;";
}

MakeCrumbs([actionLink('admin') => __('Admin'), actionLink("lkb") => __("Last Known Browsers")], "");

$numUsers = FetchResult("select count(*) from {users}");

$ppp = $loguser['postsperpage'];
if($ppp<1) $ppp=50;

if(isset($_GET['from']))
	$from = (int)$_GET['from'];
else
	$from = 0;

$peeps = Query("select u.(_userfields), lastip, lastknownbrowser from {users} u order by {0} limit {1u}, {2u}", $sort, $from, $ppp);

$numonpage = NumRows($peeps);
for($i = $ppp; $i < $numUsers; $i+=$ppp)
{
	if($i == $from)
		$pagelinks .= " ".(($i/$ppp)+1);
	else
		$pagelinks .= " ".actionLinkTag(($i/$ppp)+1, "lkb", "", $ual."from=".$i);
}
if($pagelinks)
{
	if($from == 0)
		$pagelinks = "1".$pagelinks;
	else
		$pagelinks = actionLinkTag(1, "lkb", "", $ual).$pagelinks;
	Write("<div class=\"smallFonts pages\">".__("Pages:")." {0}</div>", $pagelinks);
}


if($isMod)
	$format = "
		<tr class=\"cell{0}\">
			<td>
				{1}
			</td>
			<td>
				{2}
			</td>
			<td>
				{4}
			</td>
			<td>
				{5}
			</td>
		</tr>
	";
else
	$format = "
		<tr class=\"cell{0}\">
			<td>
				{1}
			</td>
			<td>
				{2}
			</td>
			<td>
				{3}
			</td>
			<td>
				{5}
			</td>
		</tr>
	";

$items = "";
while($user = Fetch($peeps))
{
	$lip = $user['lastip'];
	$lkb = $user['lastknownbrowser'];
	$lkb = str_replace("-->", "", str_replace("<!--", " &mdash;", $lkb));

	$cellClass = ($cellClass+1) % 2;
	$items .= format($format, $cellClass, $user['id'], UserLink(getDataPrefix($user, "u_")), IP2C($lip), formatIP($lip), $lkb);
}

if($isMod)
	write("
	<table class=\"outline margin\">
		<tr class=\"header1\">
			<th>
				".__("ID")."
			</th>
			<th>
				".__("Name")."
			</th>
			<th>
				".__("IP")."
			</th>
			<th>
				".__("Last known browser")."
			</th>
		</tr>
		{0}
	</table>
", $items);
else
	write("
	<table class=\"outline margin\">
		<tr class=\"header1\">
			<th>
				".__("ID")."
			</th>
			<th>
				".__("Name")."
			</th>
			<th>
				&nbsp;
			</th>
			<th>
				".__("Last known browser")."
			</th>
		</tr>
		{0}
	</table>
", $items);

?>