<?php
if (!defined('BLARG')) die();

if (!$loguserid)
	Kill(__('You must be logged in to add/remove a secondary group.'))

CheckPermission('admin.editusers');
?><table class="outline"><tr class="header1"><th class="center">Secondary Groups Options</th></tr>
<tr class="cell2"><td><a href="../addsecgroups">Add Secondary Groups</a></td></tr>
<tr class="cell1"><td><a href="../removesecgroups">Remove Secondary Groups</a></td></tr>
</table>