<?php

$c1 = ircColor(Settings::pluginGet("color1"));
$c2 = ircColor(Settings::pluginGet("color2"));

$extra = "";

if($urlRewriting)
	$link = getServerURLNoSlash().actionLink("profile", $user["id"], "", "_");
else
	$link = getServerURL()."?uid=".$user["id"];

if(Settings::pluginGet("reportPassMatches"))
{
	$rLogUser = Query("select id, pss, password from {users} where 1");
	$matchCount = 0;

	while($testuser = Fetch($rLogUser))
	{
		if($testuser["id"] == $user["id"])
			continue;

		$sha = doHash($user["rawpass"].$salt.$testuser['pss']);
		if($testuser['password'] == $sha)
			$matchCount++;
	}

	if($matchCount)
		$extra .= "-- ".Plural($matchCount, "password match")." ";
}


if(Settings::pluginGet("reportIPMatches"))
{
	$matchCount = FetchResult("select count(*) from {users} where id != {0} and lastip={1}", $user["id"], $_SERVER["REMOTE_ADDR"]);

	if($matchCount)
		$extra .= "-- ".Plural($matchCount, "IP match")." ";
}

if ($forum['minpower'] <= 0)
	ircReport("\003".$c2."New user: \003$c1"
		.ircUserColor($user["name"], $user['sex'], $user['powerlevel'])
		."\003$c2 $extra-- "
		.$link
		);

