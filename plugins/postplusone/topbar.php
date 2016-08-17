<?php
global $loguser;
if($post['id'] && $post['id'] != '_')
{
	$plusOne = "";
	$show = $post['postplusones'];
	
	$plusOne .= "<span class=\"plusone\">";
	if($post['u_id'] != $loguserid && $loguserid != 0)
	{
		$url = actionLink("plusone", $post["id"], "key=".$loguser["token"]);
		$url = htmlspecialchars($url);
		$plusOne .= "<a href=\"\" onclick=\"$(this.parentElement).load('$url'); return false;\">+1</a>";
		$show = true;
	}
	$plusOne .= formatPlusOnes($post["postplusones"]);
	$plusOne .= "</span>";
	if ($show)
		$extraLinks[] = $plusOne;
}