<?php 
if (HasPermission('admin.editforums')) {
	$adminLinks[] = actionLinkTag(__("Manage forum list"), "editfora");
}