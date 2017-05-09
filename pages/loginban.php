<?php
//  WorldWeb XD - Login ban management tool
if (!defined('BLARG')) die();

$title = __("Login Ban");

CheckPermission('admin.manageipbans');

MakeCrumbs([actionLink("admin") => __("Admin"), actionLink("loginban") => __("Login ban manager")]);

if(isset($http->post('actionadd'))) {
	$rLoginBan = Query("insert into {loginbans} (name) values ({0})", $http->post('name'));
	Alert(__("Added."), __("Notice"));
} elseif($http->get('action') == "delete") {
	$rLoginBan = Query("delete from {loginbans} where name={0} limit 1", $http->get('name'));
	Alert(__("Removed."), __("Notice"));
}

$rLoginBan = Query("select * from {loginbans} order by name asc");

$banList = "";
while($Loginban = Fetch($rLoginBan)) {
	$cellClass = ($cellClass+1) % 2;
	$banList .= "
	<tr class=\"cell$cellClass\">
		<td>".htmlspecialchars($loginban['name'])."</td>
		<td><a href=\"".actionLink("loginban", "", "loginname=".htmlspecialchars($Loginban['name'])."&action=delete")."\">&#x2718;</a></td>
	</tr>";
}

echo "
<table class=\"outline margin width50\">
	<tr class=\"header1\">
		<th>".__("Name")."</th>
		<th>&nbsp;</th>
	</tr>
	$banList
</table>

<form action=\"".htmlentities(pageLink("ipbans"))."\" method=\"post\" onsubmit=\"actionadd.disabled = true; return true;\">
	<table class=\"outline margin width50\">
		<tr class=\"header1\">
			<th colspan=\"2\">
				".__("Add")."
			</th>
		</tr>
		<tr>
			<td class=\"cell2\">
				".__("Name")."
			</td>
			<td class=\"cell0\">
				<input type=\"text\" name=\"name\" style=\"width: 98%;\" maxlength=\"45\" />
			</td>
		</tr>
		<tr class=\"cell2\">
			<td></td>
			<td>
				<input type=\"submit\" name=\"actionadd\" value=\"".__("Add")."\" />
			</td>
		</tr>
	</table>
</form>";