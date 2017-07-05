<?php
if (!defined('BLARG')) die();

$pluginSettings = [];
$plugins = [];
$pluginbuckets = [];
$pluginpages = [];
$plugintemplates = [];

function registerSetting($settingname, $label, $check = false)
{
	// TODO: Make this function.
}

function getSetting($settingname, $useUser = false) {
	global $pluginSettings, $user;
	if(!$useUser) { //loguser {
		if(array_key_exists($settingname, $pluginSettings))
			return $pluginSettings[$settingname]["value"];
	} else if($user['pluginsettings'] != ""); {
		$settings = unserialize($user['pluginsettings']);
		if(!is_array($settings))
			return "";
		if(array_key_exists($settingname, $settings))
			return stripslashes(urldecode($settings[$settingname]));
	}
	return "";
}

class BadPluginException extends Exception { }

// TODO cache all those data so we don't have to scan directories at each run
function getPluginData($plugin, $load = true) {
	global $pluginpages, $pluginbuckets, $plugintemplates, $misc, $abxd_version, $router;

	if(!is_dir(__DIR__."/../plugins/".$plugin))
		throw new BadPluginException("Plugin folder is gone");

	$plugindata = [];
	$plugindata['dir'] = $plugin;
	if(!file_exists(__DIR__."/../plugins/".$plugin."/plugin.settings"))
		throw new BadPluginException(__("Plugin folder doesn't contain plugin.settings"));

	$minver = 220; //we introduced these plugins in 2.2.0 so assume this.

	$settingsFile = file_get_contents(__DIR__."/../plugins/".$plugin."/plugin.settings");
	$settings = explode("\n", $settingsFile);
	foreach($settings as $setting) {
		$setting = trim($setting);
		if($setting == "") continue;
		$setting = explode("=", $setting);
		$setting[0] = trim($setting[0]);
		$setting[1] = trim($setting[1]);
		if($setting[0][0] == "#") continue;
		if($setting[0][0] == "$")
			registerSetting(substr($setting[0],1), $setting[1]);
		else
			$plugindata[$setting[0]] = $setting[1];

		if($setting[0] == "minversion")
			$minver = (int)$setting[1];
	}

	$plugindata['buckets'] = [];
	$plugindata['pages'] = [];
	$plugindata['templates'] = [];

	$dir = __DIR__."/../plugins/".$plugindata['dir'];
	$pdir = @opendir($dir);
	while($f = readdir($pdir)) {
		if(substr($f, -4) == ".php") {
			if(substr($f, 0, 5) == "page_") {
				$pagename = substr($f, 5, strlen($f) - 4 - 5);
				$plugindata["pages"][] = $pagename;
				if($load)
					$pluginpages[$pagename] = $plugindata['dir'];
			} else {
				$bucketname = substr($f, 0, -4);
				$plugindata['buckets'][] = $bucketname;
				if($load)
					$pluginbuckets[$bucketname][] = $plugindata['dir'];
			}
		}
	}
	closedir($pdir);

	if (is_dir($dir.'/pages')) {
		$pdir = @opendir($dir.'/pages');
		while($f = readdir($pdir)) {
			if(substr($f, -4) == ".php") {
				$pagename = substr($f, 0, -4);
				$plugindata['pages'][] = $pagename;
				if($load) $pluginpages[$pagename] = $plugindata['dir'];
			}
		}
		closedir($pdir);
	}

	if (is_dir($dir.'/layouts')) {
		$pdir = @opendir($dir.'/layouts');
		while($f = readdir($pdir)) {
			if(substr($f, -4) == ".tpl") {
				$tplname = substr($f, 0, -4);
				$plugindata['templates'][] = $tplname;
				if($load) $plugintemplates[$tplname] = $plugindata['dir'];
			}
		}
		closedir($pdir);
	}

	if(file_exists($dir.'/routes.yaml')) {
		$routes = spyc_load_file($dir.'/routes.yaml');
		$allRoutes = $router->getRoutes();
		$existingRoutes = [];

		foreach ($allRoutes as $route) {
			$existingRoutes[] = $route[3];
		}
		foreach ($routes as $route_name => $params) {
			if (in_array($route_name, $existingRoutes))
				throw new BadPluginException(__("Plugin route \"$route_name\" conflicts with an existing route."));
		}
		// Trust me, this is to avoid polluting the routes if there's a conflict that we dealt with earlier.
		foreach ($routes as $route_name => $params) {
			$router->map($params[0], $params[1], $params[2], $route_name);
		}
	}

	return $plugindata;
}

$rPlugins = Query("select * from {enabledplugins}");

while($plugin = Fetch($rPlugins)) {
	$plugin = $plugin["plugin"];

	try {
		$plugins[$plugin] = getPluginData($plugin);
	}
	catch(BadPluginException $e) {
		Report(Format("Disabled plugin \"{0}\" -- {1}", $plugin, $e->getMessage()));
		Query("delete from {enabledplugins} where plugin={0}", $plugin);
	}

	Settings::checkPlugin($plugin);
}



if($loguser['pluginsettings'] != "") {
	$settings = unserialize($loguser['pluginsettings']);
	if(!is_array($settings))
		$settings = [];
	foreach($settings as $setName => $setVal)
		if(array_key_exists($setName, $pluginSettings))
			$pluginSettings[$setName]["value"] = stripslashes(urldecode($setVal));
}