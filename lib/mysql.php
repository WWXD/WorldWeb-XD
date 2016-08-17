<?php
// AcmlmBoard XD support - MySQL database wrapper functions
if (!defined('BLARG')) die();

include(__DIR__."/../config/database.php");

$queries = 0;

$dblink = new mysqli($dbserv, $dbuser, $dbpass, $dbname);
unset($dbpass);

$dblink->set_charset('utf8');


function SqlEscape($text)
{
	global $dblink;
	return $dblink->real_escape_string($text);
}

function Query_ExpandFieldLists($match)
{
	$ret = array();
	$prefix = $match[1];
	$fields = preg_split('@\s*,\s*@', $match[2]);

	foreach ($fields as $f)
		$ret[] = $prefix.'.'.$f.' AS '.$prefix.'_'.$f;

	return implode(',', $ret);
}

function Query_AddUserInput($match)
{
	global $args;
	$match = $match[1];
	$format = 's';
	if(preg_match("/^\d+\D$/", $match))
	{
		$format = substr($match, strlen($match)-1, 1);
		$match = substr($match, 0, strlen($match)-1);
	}

	$var = $args[$match+1];

	if ($var === NULL) return 'NULL';
			
	if ($format == 'c')
	{
		if (empty($var)) return 'NULL';
		$final = '';
		foreach ($var as $v) $final .= '\''.SqlEscape($v).'\',';
		return substr($final,0,-1);
	}

	if($format == "i") return (string)((int)$var);
	if($format == "u") return (string)max((int)$var, 0);
	return '\''.SqlEscape($var).'\'';
}

/*
 * Function for prepared queries
 *
 * Example usage: Query("SELECT t1.(foo,bar), t2.(*) FROM {table1} t1 LEFT JOIN {table2} t2 ON t2.id=t1.crapo WHERE t1.id={0} AND t1.crapo={1}", 1337, "Robert'; DROP TABLE students; --");
 * assuming a database prefix of 'abxd_', final query is:
 * SELECT t1.foo AS t1_foo,t1.bar AS t1_bar, t2.* FROM abxd_table1 t1 LEFT JOIN abxd_table2 t2 ON t2.id=t1.crapo WHERE t1.id='1337' AND t1.crapo='Robert\'; DROP TABLE students; --'
 *
 * compacted fieldlists allow for defining certain widely-used field lists as global variables or defines (namely, the fields for usernames)
 * {table} syntax allows for flexible manipulation of table names (namely, adding a DB prefix)
 *
 */
function query()
{
	global $dbpref, $args, $fieldLists;
	$args = func_get_args();
	if (is_array($args[0])) $args = $args[0];

	$query = $args[0];

	// expand compacted field lists
	$query = preg_replace("@(\w+)\.\(\*\)@s", '$1.*', $query);
	$query = str_replace(".(_userfields)", ".(".$fieldLists["userfields"].")", $query);
	$query = preg_replace_callback("@(\w+)\.\(([\w,\s]+)\)@s", 'Query_ExpandFieldLists', $query);

	// add table prefixes
	$query = preg_replace("@\{([a-z]\w*)\}@si", $dbpref.'$1', $query);

	// add the user input
	$query = preg_replace_callback("@\{(\d+\w?)\}@s", 'Query_AddUserInput', $query);

	return RawQuery($query);
}

function rawQuery($query)
{
	global $queries, $querytext, $loguser, $dblink, $debugMode, $logSqlErrors, $dbpref, $loguserid, $mysqlCellClass;

//	if($debugMode)
//		$queryStart = usectime();

	$res = @$dblink->query($query);

	if(!$res)
	{
		$theError = $dblink->error;
		
		if($logSqlErrors)
		{
			$thequery = sqlEscape($query);
			$ip = sqlEscape($_SERVER["REMOTE_ADDR"]);
			$time = time();
			if(!$loguserid) $loguserid = 0;
			$get = sqlEscape(var_export($_GET, true));
			$post = sqlEscape(var_export($_POST, true));
			$cookie = sqlEscape(var_export($_COOKIE, true));
			$theError = sqlEscape($theError);
			$logQuery = "INSERT INTO {$dbpref}queryerrors (`user`,`ip`,`time`,`query`,`get`,`post`,`cookie`, `error`) VALUES ($loguserid, '$ip', $time, '$thequery', '$get', '$post', '$cookie', '$theError')";
			$res = @$dblink->query($logQuery);
		}
		if($debugMode)
		{
			$bt = "";
			if(function_exists("backTrace"))
				$bt = backTrace();
			die(nl2br($bt).
				"<br /><br />".htmlspecialchars($theError).
				"<br /><br />Query was: <code>".htmlspecialchars($query)."</code>");
/*				<br />This could have been caused by a database layout change in a recent git revision. Try running the installer again to fix it. <form action=\"install/doinstall.php\" method=\"POST\"><br />
			<input type=\"hidden\" name=\"action\" value=\"Install\" />
			<input type=\"hidden\" name=\"existingSettings\" value=\"true\" />
			<input type=\"submit\" value=\"Click here to re-run the installation script\" /></form>");*/
		}	
		trigger_error("MySQL Error.", E_USER_ERROR);
		die("MySQL Error.");
	}

	$queries++;

	if($debugMode)
	{
		$mysqlCellClass = ($mysqlCellClass+1)%2;
		$querytext .= "<tr class=\"cell$mysqlCellClass\"><td><pre style=\"white-space:pre-wrap;\">".htmlspecialchars(preg_replace('/^\s*/m', "", $query))."</pre></td><td>";
		if(function_exists("backTrace"))
			$querytext .= backTrace();
	}

	return $res;
}

function fetch($result)
{
	return $result->fetch_assoc();
}

function fetchRow($result)
{
	return $result->fetch_row();
}

function fetchResult()
{
	$res = Query(func_get_args());
	if($res->num_rows == 0) return -1;
	return Result($res, 0, 0);
}

// based on http://stackoverflow.com/a/3779460/736054
function result($res, $row = 0, $field = 0)
{
	$res->data_seek($row);
	$ceva = array_values($res->fetch_assoc());
	$rasp = $ceva[$field];
	return $rasp;
}

function numRows($result)
{
	return $result->num_rows;
}

function insertId()
{
	global $dblink;
	return $dblink->insert_id;
}

function affectedRows()
{
	global $dblink;
	return $dblink->affected_rows;
}

function getDataPrefix($data, $pref)
{
	$res = array();

	foreach($data as $key=>$val)
		if(substr($key, 0, strlen($pref)) == $pref)
			$res[substr($key, strlen($pref))] = $val;

	return $res;
}


$fieldLists = array(
	"userfields" => "id,name,displayname,primarygroup,sex,minipic"
);

function loadFieldLists()
{
	global $fieldLists;

	//Allow plugins to add their own!
	$bucket = "fieldLists"; include(__DIR__."/pluginloader.php");
}

?>
