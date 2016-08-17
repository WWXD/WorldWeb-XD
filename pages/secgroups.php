<?php
if (!defined('BLARG')) die();

CheckPermission('admin.editusers');

if ($_POST['userid'] && $_POST['groupid'])
{
	Query("INSERT INTO {secondarygroups} (userid,groupid) VALUES ({0},{1})",
		$_POST['userid'], $_POST['groupid']);
}

?>
<form action="" method="POST">
User ID: <input type="text" name="userid"><br>
Group ID: <input type="text" name="groupid"><br>
<input type="submit" value="Add">
</form>