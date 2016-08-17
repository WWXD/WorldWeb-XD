<?php
function formatPlusOnes($plusones)
{
	$style = "";
	if($plusones < 1)
		return '';
	$style .= "
		font-weight:bold; color:#0f0; background:black; border:1px solid #0f0; border-radius:2px; padding:1px;
		";
	return "<span style=\"$style\">+$plusones</span>";
}