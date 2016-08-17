<?php
//  AcmlmBoard XD - Board Settings editing page
//  Access: administrators
if (!defined('BLARG')) die();

$title = __("Edit settings");

CheckPermission('admin.editsettings');

$plugin = "main";
if(isset($_GET['id']))
	$plugin = $_GET['id'];
if(isset($_POST['_plugin']))
	$plugin = $_POST['_plugin'];
	
if (isset($_GET['field']))
{
	$htmlfield = $_GET['field'];
	if (!isset($settings[$htmlfield])) Kill(__('No.'));
	if ($settings[$htmlfield]['type'] != 'texthtml') Kill(__('No.'));
	
	$htmlname = $settings[$htmlfield]['name'];
}
else $htmlfield = null;

if(!ctype_alnum($plugin))
	Kill(__("No."));

if($plugin == "main")
	MakeCrumbs(array(actionLink("admin") => __("Admin"), '' => __("Edit settings")));
else
	MakeCrumbs(array(actionLink("admin") => __("Admin"), actionLink("pluginmanager") => __("Plugin manager"), '' => $plugins[$plugin]['name']));

$settings = Settings::getSettingsFile($plugin);
$oursettings = Settings::$settingsArray[$plugin];
$invalidsettings = array();

if(isset($_POST["_plugin"]))
{
	if ($_POST['key'] !== $loguser['token'])
		Kill(__('No.'));
		
	//Save the settings.
	$valid = true;

	foreach($_POST as $key => $value)
	{
		if($key == "_plugin") continue;

		//Don't accept unexisting settings.
		if(!isset($settings[$key])) continue;
		
		// don't save settings if the user isn't allowed to change them
		if ($settings[$key]['rootonly'] && !$loguser['root'])
			continue;

		//Save the entered settings for re-editing
		$oursettings[$key] = $value;

		if(!Settings::validate($value, $settings[$key]["type"], $settings[$key]["options"]))
		{
			$valid = false;
			$invalidsettings[$key] = true;
		}
		else
			Settings::$settingsArray[$plugin][$key] = $value;
	}

	if($valid)
	{
		Settings::save($plugin);
		if(isset($_POST["_exit"]))
		{
			if($plugin == "main")
				die(header("Location: ".actionLink("admin")));
			else
				die(header("Location: ".actionLink("pluginmanager")));
		}
		else
			Alert(__("Settings were successfully saved!"));
	}
	else
		Alert(__("Settings were not saved because there were invalid values. Please correct them and try again."));
}


echo "
	<form action=\"".htmlentities(actionLink("editsettings"))."\" method=\"post\">
		<input type=\"hidden\" name=\"_plugin\" value=\"$plugin\">
		<input type=\"hidden\" name=\"key\" value=\"{$loguser['token']}\">";

$settingfields = array();
$settingfields[''] = ''; // ensures the uncategorized entries come first

foreach($settings as $name => $data)
{
	if ($data['rootonly'] && !$loguser['root'])
		continue;
		
	if ($data['type'] == 'texthtml' && $htmlfield == null)
		continue;
	if ($htmlfield != null && $htmlfield != $name)
		continue;
		
	$sdata = array();
		
	$sdata['name'] = $name;
	if(isset($data['name']))
		$sdata['name'] = $data['name'];

	$type = $data['type'];
	$help = $data['help'];
	$options = $data['options'];
	$value = $oursettings[$name];

	$input = "[Bad setting type]";

	$value = htmlspecialchars($value);

	if($type == "boolean")
		$input = makeSelect($name, $value, array(1=>"Yes", 0=>"No"));
	else if($type == "options")
		$input = makeSelect($name, $value, $options);
	else if($type == "integer" || $type == "float")
		$input = "<input type=\"text\" id=\"$name\" name=\"$name\" value=\"$value\" />";
	else if($type == "text")
		$input = "<input type=\"text\" id=\"$name\" name=\"$name\" value=\"$value\" class=\"width75\"/>";
	else if($type == "password")
		$input = "<input type=\"password\" id=\"$name\" name=\"$name\" value=\"$value\" class=\"width75\"/>";
	else if($type == "textbox" || $type == "textbbcode")
		$input = "<textarea id=\"$name\" name=\"$name\" rows=\"8\">\n$value</textarea>";
	else if($type == "texthtml")
		$input = "<textarea id=\"$name\" name=\"$name\" rows=\"30\">\n$value</textarea>";
	else if($type == "forum")
		$input = makeForumList($name, $value, true);
	else if ($type == 'group')
		$input = makeSelect($name, $value, $grouplist);
	else if($type == "theme")
		$input = makeThemeList($name, $value);
	else if($type == "layout")
		$input = makeLayoutList($name, $value);
	else if($type == "language")
		$input = makeLangList($name, $value);
		
	$sdata['field'] = $input;

	if($invalidsettings[$name])
		$sdata['name'] = "<span style=\"color: #f44;\">{$sdata['name']} (invalid)</span>";

	if($help)
		$sdata['name'] .= "<br><small>$help</small>";

	$settingfields[$data['category']][] = $sdata;
}

if (!$settingfields['']) unset($settingfields['']);

$fields = array(
	'btnSaveExit' => "<input type=\"submit\" name=\"_exit\" value=\"".__("Save and Exit")."\">",
	'btnSave' => "<input type=\"submit\" name=\"_action\" value=\"".__("Save")."\">",
);

RenderTemplate('form_settings', array('settingfields' => $settingfields, 'htmlfield' => $htmlfield, 'fields' => $fields));

echo "
	</form>";
	
	

function makeSelect($fieldName, $checkedIndex, $choicesList, $extras = "")
{
	$checks[$checkedIndex] = " selected=\"selected\"";
	foreach($choicesList as $key=>$val)
		$options .= format("
						<option value=\"{0}\"{1}>{2}</option>", $key, $checks[$key], $val);
	$result = format(
"
					<select id=\"{0}\" name=\"{0}\" size=\"1\" {1} >{2}
					</select>", $fieldName, $extras, $options);
	return $result;
}

function makeThemeList($fieldname, $value)
{
	$themes = array();
	$dir = @opendir("themes");
	while ($file = readdir($dir))
	{
		if ($file != "." && $file != "..")
		{
			$name = explode("\n", @file_get_contents("./themes/".$file."/themeinfo.txt"));
			$themes[$file] = trim($name[0]);
		}
	}
	closedir($dir);
	return makeSelect($fieldname, $value, $themes);
}

function makeLayoutList($fieldname, $value)
{
	$layouts = array();
	$dir = @opendir("layouts");
	while ($file = readdir($dir))
	{
		if (endsWith($file, ".php"))
		{
			$layout = substr($file, 0, strlen($file)-4);
			$layouts[$layout] = @file_get_contents("./layouts/".$layout.".info.txt");
		}
	}
	closedir($dir);
	return makeSelect($fieldname, $value, $layouts);
}

function makeLangList($fieldname, $value)
{
	$data = array();
	$dir = @opendir("lib/lang");
	while ($file = readdir($dir))
	{
		//print $file;
		if (endsWith($file, "_lang.php"))
		{
			$file = substr($file, 0, strlen($file)-9);
			$data[$file] = $file;
		}
	}
	$data["en_US"] = "en_US";
	closedir($dir);
	return makeSelect($fieldname, $value, $data);
}

//From the PHP Manual User Comments
// ... this is unused?
function foldersize($path)
{
	$total_size = 0;
	$files = scandir($path);
	$files = array_slice($files, 2);
	foreach($files as $t)
	{
		if(is_dir($t))
		{
			//Recurse here
			$size = foldersize($path . "/" . $t);
			$total_size += $size;
		}
		else
		{
			$size = filesize($path . "/" . $t);
			$total_size += $size;
		}
	}
	return $total_size;
}

?>
