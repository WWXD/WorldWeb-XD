<?php
if (!defined('BLARG')) die();

$title = "Add-on Manager";

CheckPermission('admin.editsettings');

MakeCrumbs([actionLink("admin") => __("Admin"), actionLink("addonmanager") => __("Add-on Manager")]);

$enabledfile = BOARD_ROOT.'plugins/'.$plugin.'/enabled.txt';

if($_REQUEST['action'] == "enable") {
	if($_REQUEST['key'] != $loguser['token'])
		Kill("No.");

	Query("insert into {enabledplugins} values ({0})", $_REQUEST['addon']);
	require(BOARD_ROOT.'db/functions.php');
	Upgrade();

	//Make a new file for easier detecting that it is enabled
	if (!file_put_contents($enabledfile, 'This is a holdertext file that signifies that this add-on is enabled. Don\'t delete this file.')) {
		Report("[b]".$loguser['name']."[/] tried to add a add-on called ".$_REQUEST['addon']." but failed.", false);
		Alert(__("Sorry, but the add-on couldn't be added by our file detection usage. Please report this to the website's owner."), __("Error"));
	} else {
		Report("[b]".$loguser['name']."[/] successfully added an add-on called ".$_REQUEST['addon'].".", false);
		Alert(__("You have successfully added the add-on."), __("Success"));
	}

	die(header("location: ".actionLink("addonmanager")));
}

if($_REQUEST['action'] == "disable") {
	if($_REQUEST['key'] != $loguser['token'])
		Kill("No.");

	Query("delete from {enabledplugins} where plugin={0}", $_REQUEST['addon']);

	//Delete the enabled text.
	if (file_exists($enabledfile)) {
		if(!unlink($enabledfile)) {
			Report("[b]".$loguser['name']."[/] tried to remove a add-on called ".$_REQUEST['addon']." but failed.", false);
			Alert(__("Sorry, but the add-on couldn't be removed by our file detection usage. Please report this to the website's owner."), __("Error"));
		} else {
			Report("[b]".$loguser['name']."[/] successfully removed an add-on called ".$_REQUEST['addon'].".", false);
			Alert(__("You have successfully removed the add-on."), __("Success"));
		}
	}

	die(header("location: ".actionLink("addonmanager")));
}

$cell = 0;
$pluginsDir = @opendir(BOARD_ROOT."plugins");

$enabledplugins = [];
$disabledplugins = [];
$pluginDatas = [];

if($pluginsDir !== FALSE) {
	while(($plugin = readdir($pluginsDir)) !== FALSE) {
		if($plugin == "." || $plugin == "..") continue;
		if(is_dir(BOARD_ROOT."plugins/".$plugin)) {
			try {
				$plugindata = getPluginData($plugin, false);
			}
			catch(BadPluginException $e) {
				continue;
			}

			$pluginDatas[$plugin] = $plugindata;
			if(isset($plugins[$plugin]))
				$enabledplugins[$plugin] = $plugindata['name'];
			else
				$disabledplugins[$plugin] = $plugindata['name'];
		}
	}

}

asort($enabledplugins);
asort($disabledplugins);

$ep = [];
$dp = [];

foreach($enabledplugins as $plugin => $pluginname)
	$ep[] = listPlugin($plugin, $pluginDatas[$plugin]);

foreach($disabledplugins as $plugin => $pluginname)
	$dp[] = listPlugin($plugin, $pluginDatas[$plugin]);

RenderTemplate('pluginlist', ['enabledPlugins' => $ep, 'disabledPlugins' => $dp]);


function listPlugin($plugin, $plugindata) {
	global $plugins, $loguser;

	$pdata = $plugindata;

	$hasperms = false;
	if (!isset($plugins[$plugin]) && file_exists(BOARD_ROOT.'plugins/'.$plugin.'/permStrings.php'))
		$hasperms = true;

	if ($hasperms)
		$pdata['description'] .= '<br><strong>This plugin has permissions. After enabling it, make sure to configure them properly.</strong>';

	if(isset($plugins[$plugin])) {
		$text = __("Disable");
		$act = "disable";
	} else {
		$text = __("Enable");
		$act = "enable";
	}

	$pdata['actions'] = '<ul class="pipemenu">'.actionLinkTagItem($text, "addonmanager", '', "addon=".$plugin."&action=".$act."&key=".$loguser['token']);

	if(in_array("settingsfile", $plugindata['buckets'])) {
		if(isset($plugins[$plugin]))
			$pdata['actions'] .= actionLinkTagItem(__("Settings&hellip;"), "editsettings", '', 'addon='.$plugin);
	}
	$pdata['actions'] .= '</ul>';

	return $pdata;
}