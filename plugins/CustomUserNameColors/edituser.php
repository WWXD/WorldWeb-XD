<?php

$canhavenamecolor = HasPermission('user.editnamecolor') || $editUserMode;

if(!function_exists("HandleUsernameColor"))
{
	function handleUsernameColor($field, $item)
	{
		global $user, $loguser;

		if ($canhavenamecolor)
		{
			$unc = $_POST['color'];
			if($unc != "")
				$unc = filterPollColors(str_pad($unc, 6, '0'));

			Query("UPDATE {users} SET color={0s} WHERE id={1}", $unc, $user["id"]);
		}
		return true;
	}
}

if ($canhavenamecolor)
{
	write("<script type=\"text/javascript\" src=\"".resourceLink("js/jscolor/jscolor.js")."\"></script>");
	$general['appearance']['items']['color'] = array(
		"caption" => "Name color",
		"type" => "text",
		"before" => "#",
		"length" => 6,
		"more" => "class=\"color {hash:false,required:false,pickerFaceColor:'black',pickerFace:3,pickerBorder:0,pickerInsetColor:'black',pickerPosition:'left',pickerMode:'HVS'}\"",
		"callback" => "handleUsernameColor",
	);
}


