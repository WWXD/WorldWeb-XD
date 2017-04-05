<?php
if (!defined('BLARG')) die();

CheckPermission('admin.editusers');

if ($http->post('submit')) {
	if ($http->post('userid') && $http->post('groupid')) {
		Query("INSERT INTO {secondarygroups} (userid,groupid) VALUES ({0},{1})",
			$http->post('userid'), $http->post('groupid'));
		Report("[b]".$loguser['name']."[/] successfully added a secondary group (ID: ".$_POST['groupid'].") to user ID #".$_POST['userid'].".", false);
		Alert(__("Secondary group successfully added."), __("Notice"));
	} else if (!$http->post('userid') && $http->post('groupid')) {
		Report("[b]".$loguser['name']."[/] tried to add a secondary group (ID: ".$http->post('groupid').") from someone.", false);
		Alert(__("Please enter a User ID and try again."), __("Notice"));
	} else if ($http->post('userid') && !$http->post('groupid')) {
		Report("[b]".$loguser['name']."[/] tried to add a secondary group from user ID #".$http->post('userid').".", false);
		Alert(__("Please enter a Group ID and try again."), __("Notice"));
	} else if (!$http->post('userid') && !$http->post('groupid')) {
		Report("[b]".$loguser['name']."[/] tried to add a secondary group.", false);
		Alert(__("Please enter a Group ID and a User ID and try again."), __("Notice"));
	}
} else {
	Alert(__("Please enter a Group ID and a User ID."), __("Notice"));
}
?>
<table class="outline"><tr class="header1"><th colspan="2" class="center">Add secondary groups</th></tr>
<form action="" method="POST" onsubmit="submit.disabled = true; return true;">
<tr class="cell2"><td>User ID</td><td><input type="text" name="userid"></td></tr>
<tr class="cell1"><td>Group ID</td><td><input type="text" name="groupid"></td></tr>
<tr><td colspan="2" class="cell2"><input type="submit" name="submit" value="Add"></td></tr>
</form>
</table>
