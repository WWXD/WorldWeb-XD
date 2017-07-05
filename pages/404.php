<?php
//  WorldWeb XD - 404
//  Access: all
if (!defined('BLARG')) die();

header("HTTP/1.0 404 Not Found");
header('HTTP/1.1 404 Not Found');
header("HTTP/2.0 404 Not Found");
header('Status: 404 Not Found');

$title = __("404 - Not found");

//echo $_SERVER['REQUEST_URI'].' -- '.$_SERVER['SCRIPT_NAME'].'?'.$_SERVER['QUERY_STRING'];

Kill(__('The page you are looking for was not found.').'<br /><br />
	<a href="../">'.__('Return to the board index').'</a>', __("404 - Not found"));