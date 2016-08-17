<?php

if (!Settings::pluginGet('forumid')) continue;
if($forum['id'] == Settings::pluginGet('forumid'))
{
	$rndTitles = file_get_contents("./plugins/".Settings::pluginGet('dir']."/titles.txt");
	$rndTitles = explode("\n", $rndTitles);
	$rndDescs = file_get_contents("./plugins/".Settings::pluginGet('dir']."/descs.txt");
	$rndDescs = explode("\n", $rndDescs);

	$forum['title'] = $rndTitles[array_rand($rndTitles)];
	$forum['description'] = $rndDescs[array_rand($rndDescs)];
}

?>
