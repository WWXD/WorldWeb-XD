<?php

function formatPlusOnes($plusones)
{
	$style = "";

	if($plusones < 1)
		return '';

	$style .= "
		position:relative;
		";
	$style2 .= "
		position:absolute;
		top: 1em;
		right: 0;
		font-size:150%;
		font-weight: bold;
		border-radius: 5px;
		padding: 2px;
		background: rgba(0,64,0,0.5);
		border: #4f4 1px solid;
		color:white;";

	return "<span style=\"$style\"><span style=\"$style2\">+$plusones</span></span>";
}
