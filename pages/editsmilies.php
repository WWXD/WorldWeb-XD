<?php
//  AcmlmBoard XD - Smiley editing tool
//  Access: administrators only

CheckPermission('admin.editsmilies');

MakeCrumbs([actionLink("admin") => __("Admin"), actionLink("editsmilies") => __("Edit smilies")]);

if (isset($http->post('action')) && $loguser['token'] != $http->post('key'))
	Kill(__("No."));

if($http->post('action') == "Apply") {
	$rSmilies = Query("select * from {smilies}");
	$numSmilies = NumRows($rSmilies);

	for($i = 0; $i <= $numSmilies; $i++) {
		if($http->post('code_'.$i) != $http->post('oldcode_'.$i) || $http->post('image_'.$i) != $http->post('oldimage_'.$i)) {
			if($http->post('code_'.$i) == "") {
				$act = "deleted";
				$rSmiley = Query("delete from {smilies} where code={0}", $http->post('oldcode_'.$i));
			} else {
				$act = "edited to \"".$http->post('image_'.$i)."\"";
				$rSmiley = Query("update {smilies} set code={0}, image={1} where code={2}", $http->post('code_'.$i), $http->post('image_'.$i), $http->post('oldcode_'.$i));
			}
			$log .= "Smiley \"".$http->post('oldcode_'.$i)."\" ".$act.".<br />";
		}
	}

	if($http->post('code_add') && $http->post('image_add')) {
		$rSmiley = Query("insert into {smilies} (code,image) value ({0}, {1})", $http->post('code_add'), $http->post('image_add'));
		$log .= "Smiley \"".$http->post('code_add')."\" added.<br />";
	}
	if($log)
		Alert($log,"Log");
}

$smileyList = "";
$rSmilies = Query("select * from {smilies}");
while($smiley = Fetch($rSmilies)) {
	$cellClass = ($cellClass+1) % 2;
	$i++;

	$smileyList .= format(
"
			<tr class=\"cell{0}\">
				<td>
					<input type=\"text\" name=\"code_{1}\" value=\"{2}\" />
					<input type=\"hidden\" name=\"oldcode_{1}\" value=\"{2}\" />
				</td>
				<td>
					<input type=\"text\" name=\"image_{1}\" value=\"{3}\" />
					<input type=\"hidden\" name=\"oldimage_{1}\" value=\"{3}\" />
				</td>
				<td>
					<img src=\"{4}\" alt=\"{5}\" title=\"{5}\">
				</td>
			</tr>
",	$cellClass, $i, $smiley['code'], $smiley['image'],
	htmlspecialchars(resourceLink("img/smilies/".$smiley['image'])), $smiley['code']);
}

write(
"
	<div class=\"outline margin width25 faq\">
		To add, fill in both bottom fields and apply.<br />
		To edit, change either code or image fields to <em>not</em> match their hidden counterparts.
	</div>

	<form method=\"post\" action=\"".htmlentities(pageLink("editsmilies"))."\" onsubmit=\"action.disabled = true; return true;\">

		<table class=\"outline margin\" style=\"width: 30%;\">
			<tr class=\"header1\">
				<th>
					Code
				</th>
				<th  colspan=\"2\">
					Image
				</th>
			</tr>
			{0}
			<tr class=\"header0\">
				<th colspan=\"3\">
					Add
				</th>
			</tr>
			<tr class=\"cell2\">
				<td>
					<input type=\"text\" name=\"code_add\" />
				</td>
				<td colspan=\"2\">
					<input type=\"text\" name=\"image_add\" />
				</td>
			</tr>

			<tr class=\"cell2\">
				<td colspan=\"3\">
					<input type=\"submit\" name=\"action\" value=\"Apply\" />
					<input type=\"hidden\" name=\"key\" value=\"{1}\" />
				</td>
			</tr>

		</table>
	</form>
", $smileyList, $loguser['token']);

?>