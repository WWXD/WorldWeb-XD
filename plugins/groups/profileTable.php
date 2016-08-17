<?php

	$groups = array();
	$qGroups = "select name from {groups} left join {groupaffiliations} on {groups}.id = {groupaffiliations}.gid where uid = {0} and status = 0";
	$rGroups = Query($qGroups, $user['id']);
	while($group = Fetch($rGroups))
		$groups[] = $group['name'];
	$groups = implode(", ", $groups);

	if($groups)
		$profileParts['General information']['Groups'] = $groups;

?>