<?php
if (!defined('BLARG')) die();

function backTrace()
{
	$backtrace = debug_backtrace();
	foreach ($backtrace as $bt) {
		$args = '';
		foreach ($bt['args'] as $a) {
			if ($args) {
				$args .= ', ';
			}
			if (in_array(strtolower($bt['function']), array('rawquery', 'query', 'fetchresult')) && !$args)
				if (is_array($a))
					$args .= var_export(array_merge(array("..."), array_slice($a, 1)), true);
				else if (is_string($a))
					$args .= "'...'";
				else
					$args .= '???';
			else
				$args .= var_export($a, true);
		}
		$bt["file"] = substr($bt["file"], strlen($_SERVER["DOCUMENT_ROOT"]));
		
		if(strlen($args) > 50)
			$args = substr($args, 0, 50)."...";
		$output .= htmlspecialchars($bt['file']).":".htmlspecialchars($bt['line'])." &nbsp; ";
		$output .= htmlspecialchars("{$bt['class']}{$bt['type']}{$bt['function']}($args)");
		$output .= "<br />";
	}
	return $output;
}


function var_format($v) // pretty-print var_export
{
	return (str_replace(array("\n"," ","array"),
array("<br />","&nbsp;","&nbsp;<i>array</i>"),
var_export($v,true))."<br />");
}
