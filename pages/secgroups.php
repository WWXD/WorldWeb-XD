<?php
if (!defined('BLARG')) die();

CheckPermission('admin.editusers');

if ($_POST['userid'] && $_POST['groupid'])
{
	Query("INSERT INTO {secondarygroups} (userid,groupid) VALUES ({0},{1})",
		$_POST['userid'], $_POST['groupid']);
}

?><table><tr><th colspan="2" class="header1 center">Add secondary groups</th></tr>
<form action="" method="POST">
<tr class="cell2"><td>User ID</td><td><input type="text" name="userid"></td></tr>
<tr class="cell1"><td>Group ID</td><td><input type="text" name="groupid"></td></tr>
<tr><td colspan="2" class="cell2"><input type="submit" value="Add"></td></tr>
</form>
</table>
