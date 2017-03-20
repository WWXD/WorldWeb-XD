<?php
if (!defined('BLARG')) die();

header('Cache-control: no-cache, private');
header('X-Frame-Options: DENY');

// I can't believe there are PRODUCTION servers that have E_NOTICE turned on. What are they THINKING? -- Kawa
error_reporting(E_ALL ^ E_NOTICE | E_STRICT);

define('BLARG_VERSION', '1.2');

define('BOARD_ROOT', dirname(__DIR__).'/');
define('DATA_DIR', BOARD_ROOT.'data/');

$boardroot = preg_replace('{/[^/]*$}', '/', $_SERVER['SCRIPT_NAME']);
define('URL_ROOT', $boardroot);
define('DATA_URL', URL_ROOT.'data/');

setlocale(LC_ALL, 'en_US.UTF8');

if(!is_file(__DIR__.'/../config/database.php'))
	die(header('Location: install.php'));

if (!function_exists('password_hash'))
	require_once('passhash.php');


// Deslash GPC variables if we have magic quotes on
if (get_magic_quotes_gpc()) {
	function AutoDeslash($val) {
		if (is_array($val))
			return array_map('AutoDeslash', $val);
		else if (is_string($val))
			return stripslashes($val);
		else
			return $val;
	}

	$_REQUEST = array_map('AutoDeslash', $_REQUEST);
	$_GET = array_map('AutoDeslash', $_GET);
	$_POST = array_map('AutoDeslash', $_POST);
	$_COOKIE = array_map('AutoDeslash', $_COOKIE);
}

function usectime() {
	$t = gettimeofday();
	return $t['sec'] + ($t['usec'] / 1000000);
}


// undocumented feature: multiple 'boards'.
// add in here to add board sections to your board
$forumBoards = array('' => 'Main forums');


require_once(__DIR__."/../config/salt.php");

require_once(__DIR__."/settingsfile.php");

require_once(__DIR__."/debug.php");
require_once(__DIR__."/mysql.php");
require_once(__DIR__."/../config/database.php");
if(!fetch(query("SHOW TABLES LIKE '{misc}'")))
	die("The boards tables are empty. Please copy the db folder and install.php to your board root and delete the config folder from the root. Overwrite any files if nessesary.");
require_once(__DIR__."/settingssystem.php");
Settings::load();
Settings::checkPlugin("main");

require_once(__DIR__."/functions.php");
require_once(__DIR__."/language.php");
require_once(__DIR__."/links.php");

class KillException extends Exception { }
date_default_timezone_set("GMT");
$timeStart = usectime();

$title = "";

//WARNING: These things need to be kept in a certain order of execution.

$thisURL = $_SERVER['SCRIPT_NAME'];
if($q = $_SERVER['QUERY_STRING'])
	$thisURL .= "?$q";

require_once(__DIR__."/browsers.php");
require_once(__DIR__."/pluginsystem.php");
loadFieldLists();
require_once(__DIR__."/loguser.php");
require_once(__DIR__."/permissions.php");

if (Settings::get('maintenance') && !$loguser['root'] && (!isset($_GET['page']) || $_GET['page'] != 'login'))
	$_GET["page"] = "maintenance";

require_once(__DIR__."/notifications.php");
require_once(__DIR__."/firewall.php");
require_once(__DIR__."/ranksets.php");
require_once(__DIR__."/bbcode_parser.php");
require_once(__DIR__."/bbcode_text.php");
require_once(__DIR__."/bbcode_callbacks.php");
require_once(__DIR__."/bbcode_main.php");
require_once(__DIR__."/post.php");
require_once(__DIR__."/onlineusers.php");

$theme = $loguser['theme'];
require_once(__DIR__."/layout.php");

//Classes

require_once(__DIR__."/smarty/Smarty.class.php");
$tpl = new Smarty;
$tpl->assign('config', array('date' => $loguser['dateformat'], 'time' => $loguser['timeformat']));
$tpl->assign('loguserid', $loguserid);
$tpl->setCompileDir('/tmp/templates_compiled');
$tpl->setCacheDir('/tmp/templates_cache');
require_once(__DIR__."/../class/PipeMenuBuilder.php");

$bucket = "init"; include(__DIR__."/pluginloader.php");

