<?php
//  AcmlmBoard XD Plugin - User badges management tool
//  Access: administrators only
//  Based on ipbans.php

$title = __("User Badges Manager");

assertForbidden("editUserBadges");

if($loguser['powerlevel'] < 3)
	kill(__("You're not an administrator. There is nothing for you here."));


$crumbs = new PipeMenu();
$crumbs->add(new PipeMenuLinkEntry(__("Admin"), "admin"));
$crumbs->add(new PipeMenuLinkEntry(__("User Badges Manager"), "userbadges"));
makeBreadcrumbs($crumbs);

if($_POST['action'] == __("Add"))
{
	if($_POST['color'] == -1 || empty($_POST['userid']) || empty($_POST['name']))
	{
		kill(__("Please review your settings before adding a user badge."));
	}
	else
	{
		query("insert into {badges} values ({0}, {1}, {2})",
		(int)$_POST['userid'], $_POST['name'], (int)$_POST['color']);

		alert(__("Added."), __("Notice"));
	}
}
elseif($_GET['action'] == "delete")
{
	query("delete from {badges} where owner = {0} and name = {1}",
		(int)$_GET['userid'], $_GET['name']);

	alert(__("Removed."), __("Notice"));
}
elseif($_GET['action'] == "deleteall")
{
	query("delete from {badges} where owner = {0}",
	(int)$_GET['userid']);
	alert(__("Removed all badges of the user."), __("Notice"));
}
elseif($_GET['action'] == "newbadge")
{
	$userID = "value=\"".((int)$_GET['userid'])."\"";
}


// Fetch badges
$qBadge = "SELECT owner, {badges}.name, {badges}.color, {users}.name username, {users}.sex sex, {users}.powerlevel powerlevel FROM {badges} JOIN {users} where owner = id";
$rBadge = query($qBadge);


$badgeList = "";
while($badges = Fetch($rBadge))
{
	$cellClass = ($cellClass+1) % 2;
	$colors = array(__("Bronze"),__("Silver"),__("Gold"),__("Platinum"));

	$badgeList .= format(
"
	<tr class=\"cell{0}\">
		<td>
			<a href=".actionLink("profile", "{2}").">{1}</a>
		</td>
		<td>
			{3}
		</td>
		<td>
			{4}
		</td>
		<td>
			<a href=\"".actionLink("userbadges", "", "userid={2}&name={3}&action=delete")."\">&#x2718;</a>
		</td>
	</tr>
", $cellClass, $badges['username'], $badges['owner'], $badges['name'], $colors[$badges['color']]);
}

write("
<table class=\"outline margin width50\">
	<tr class=\"header1\">
		<th>".__("Badge Owner")."</th>
		<th>".__("Badge Name")."</th>
		<th>".__("Badge Type")."</th>
		<th>&nbsp;</th>
	</tr>
	{0}
</table>

<form action=\"".actionLink("userbadges")."\" method=\"post\">
	<table class=\"outline margin width50\">
		<tr class=\"header1\">
			<th colspan=\"2\">
				".__("Add")."
			</th>
		</tr>
		<tr>
			<td class=\"cell2\">
				".__("User ID")."
			</td>
			<td class=\"cell0\">
				<input type=\"text\" name=\"userid\" style=\"width: 15%;\" maxlength=\"4\" {1}/>
			</td>
		</tr>
		<tr>
			<td class=\"cell2\">
				".__("Name")."
			</td>
			<td class=\"cell1\">
				<input type=\"text\" name=\"name\" style=\"width: 98%;\" maxlength=\"15\" />
			</td>
		</tr>
		<tr>
			<td class=\"cell2\">
				".__("Type")."
			</td>
			<td class=\"cell1\">
				<select name=\"color\">
					<option value=\"-1\">".__("Select")."</option>
					<option value=\"0\">".__("Bronze")."</option>
					<option value=\"1\">".__("Silver")."</option>
					<option value=\"2\">".__("Gold")."</option>
					<option value=\"3\">".__("Platinum")."</option>
				</select>
			</td>
		</tr>
		<tr class=\"cell2\">
			<td></td>
			<td>
				<input type=\"submit\" name=\"action\" value=\"".__("Add")."\" />
			</td>
		</tr>
	</table>
</form>
", $badgeList, $userID);

?>
