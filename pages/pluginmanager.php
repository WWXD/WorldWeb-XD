<?php
if (!defined('BLARG')) die();

$title = "Plugin Manager";

CheckPermission('admin.editsettings');

MakeCrumbs(array(actionLink("admin") => __("Admin"), actionLink("pluginmanager") => __("Plugin Manager")));


if($_REQUEST['action'] == "enable")
{
	if($_REQUEST['key'] != $loguser['token'])
		Kill("No.");

	Query("insert into {enabledplugins} values ({0})", $_REQUEST['id']);
	require(BOARD_ROOT.'db/functions.php');
	Upgrade();

	die(header("location: ".actionLink("pluginmanager")));
}
if($_REQUEST['action'] == "disable")
{
	if($_REQUEST['key'] != $loguser['token'])
		Kill("No.");

	Query("delete from {enabledplugins} where plugin={0}", $_REQUEST['id']);
	die(header("location: ".actionLink("pluginmanager")));
}


$cell = 0;
$pluginsDir = @opendir(BOARD_ROOT."plugins");

$enabledplugins = array();
$disabledplugins = array();
$pluginDatas = array();

if($pluginsDir !== FALSE)
{
	while(($plugin = readdir($pluginsDir)) !== FALSE)
	{
		if($plugin == "." || $plugin == "..") continue;
		if(is_dir(BOARD_ROOT."plugins/".$plugin))
		{
			try
			{
				$plugindata = getPluginData($plugin, false);
			}
			catch(BadPluginException $e)
			{
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

$ep = array();
$dp = array();

foreach($enabledplugins as $plugin => $pluginname)
	$ep[] = listPlugin($plugin, $pluginDatas[$plugin]);

foreach($disabledplugins as $plugin => $pluginname)
	$dp[] = listPlugin($plugin, $pluginDatas[$plugin]);

RenderTemplate('pluginlist', array('enabledPlugins' => $ep, 'disabledPlugins' => $dp));


function listPlugin($plugin, $plugindata)
{
	global $plugins, $loguser;
	
	$pdata = $plugindata;
	
	$hasperms = false;
	if (!isset($plugins[$plugin]) && file_exists(BOARD_ROOT.'plugins/'.$plugin.'/permStrings.php'))
		$hasperms = true;
		
	if ($hasperms)
		$pdata['description'] .= '<br><strong>This plugin has permissions. After enabling it, make sure to configure them properly.</strong>';

		
	$text = __("Enable");
	$act = "enable";
	if(isset($plugins[$plugin]))
	{
		$text = __("Disable");
		$act = "disable";
	}
	$pdata['actions'] = '<ul class="pipemenu">'.actionLinkTagItem($text, "pluginmanager", $plugin, "action=".$act."&key=".$loguser['token']);

	if(in_array("settingsfile", $plugindata['buckets']))
	{
		if(isset($plugins[$plugin]))
			$pdata['actions'] .= actionLinkTagItem(__("Settings&hellip;"), "editsettings", $plugin);
	}
	$pdata['actions'] .= '</ul>';
	
	return $pdata;
}

?>
