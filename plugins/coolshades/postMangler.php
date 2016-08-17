<?php

if($loguser['powerlevel'] > 0)
{
	$postText = str_replace("<!--", "<span style=\"color: #66ff66;\">&lt;!--", $postText);
	$postText = str_replace("-->", "--&gt;</span>", $postText);
}

?>