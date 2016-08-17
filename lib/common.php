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


if(!is_file(__DIR__.'/../config/database.php'))
	die(header('Location: install.php'));


// Deslash GPC variables if we have magic quotes on
if (get_magic_quotes_gpc())
{
	function AutoDeslash($val)
	{
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

function usectime()
{
	$t = gettimeofday();
	return $t['sec'] + ($t['usec'] / 1000000);
}


// undocumented feature: multiple 'boards'.
// add in here to add board sections to your board
$forumBoards = array('' => 'Main forums');


include(__DIR__."/../config/salt.php");

include(__DIR__."/settingsfile.php");

include(__DIR__."/debug.php");
include(__DIR__."/mysql.php");
include(__DIR__."/settingssystem.php");
Settings::load();
Settings::checkPlugin("main");

include(__DIR__."/functions.php");
include(__DIR__."/language.php");
include(__DIR__."/links.php");

class KillException extends Exception { }
date_default_timezone_set("GMT");
$timeStart = usectime();

$title = "";

//WARNING: These things need to be kept in a certain order of execution.

$thisURL = $_SERVER['SCRIPT_NAME'];
if($q = $_SERVER['QUERY_STRING'])
	$thisURL .= "?$q";

include(__DIR__."/pluginsystem.php");
loadFieldLists();
include(__DIR__."/loguser.php");
include(__DIR__."/permissions.php");

if (Settings::get('maintenance') && !$loguser['root'] && (!isset($_GET['page']) || $_GET['page'] != 'login'))
{
	die('The board is in maintenance mode, please try again later. Our apologies for the inconvenience.');
}

include(__DIR__."/notifications.php");
include(__DIR__."/firewall.php");
include(__DIR__."/ranksets.php");
include(__DIR__."/bbcode_parser.php");
include(__DIR__."/bbcode_text.php");
include(__DIR__."/bbcode_callbacks.php");
include(__DIR__."/bbcode_main.php");
include(__DIR__."/post.php");
include(__DIR__."/onlineusers.php");

$theme = $loguser['theme'];
include(__DIR__."/layout.php");

//Classes

include(__DIR__."/smarty/Smarty.class.php");
$tpl = new Smarty;
$tpl->assign('config', array('date' => $loguser['dateformat'], 'time' => $loguser['timeformat']));
$tpl->assign('loguserid', $loguserid);

$bucket = "init"; include(__DIR__."/pluginloader.php");

