<?php
for($i = 0; $i < Settings::pluginGet('numberOfFields'); $i++)
{
	if(getSetting("profileExt".$i."t", true) != "" && getSetting("profileExt".$i."v", true) != "")
	{
		$profileParts[__('Other stuff')][strip_tags(getSetting("profileExt".$i."t", true))] = CleanUpPost(getSetting("profileExt".$i."v", true));
	}
}

?>
