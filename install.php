<!doctype html>
<html>
<head>
<title>Blargboard XD Install</title>
<style type="text/css">
	@import url(http://fonts.googleapis.com/css?family=Open+Sans);

	html, body { width: 100%; height: 100%; }
	
	body {
		background: #0d0d0d;
		font-family: "Verdana", "Arial", sans-serif;
		color: #dedede; }
	
	.container {
		max-width: 1366px;
		margin-left: auto;
		margin-right: auto; }
	
	.outline {
		margin-bottom: 15px; }
		
	.box {
		border-top: 1px solid #383838;
		border-left: 1px solid #383838;
		border-right: 1px solid #000;
		border-bottom: 1px solid #000;
		padding: 5px; }
		
	.header {
		background: #1a1a1a;
		padding: 4px 8px; }
		
	.cell {
		background: #232323; }
	
	.col2 {
		background: #1e1e1e; }
		
	.center {
		text-align: center; }
		
	#boardtitle {
		font-size: 5em;
		color: #fff;
		margin-top: 5px;
		text-align: center;
		font-family: 'Open Sans'; 
		font-weight: normal; }

	#subtitle {
		font-size: 2em;
		opacity: 0.5;
		margin-bottom: 5px; 
		color: #fff;
		text-align: center;
		font-family: 'Open Sans'; 
		font-weight: normal; }
		
	textarea, input {
		color: #eeeeee; }

	textarea, input[type='text'], input[type='password'], input[type='email'] {
		border: 1px solid #555555;
		background: #333333;
		padding: 4px }

	textarea:hover, input[type='text']:hover, input[type='password']:hover, input[type='email']:hover {
		border: 1px solid #888; }

	textarea:focus, textarea:active, input[type='text']:focus, input[type='text']:active, input[type='password']:focus, input[type='password']:active, input[type='email']:focus, 	input[type='email']:active {
		border: 1px solid #999;
		background: #484848; }
	
	button, input[type='submit'] {
		background: #1a1a1a;
		border-top: 1px solid #383838;
		border-left: 1px solid #383838;
		border-bottom: 1px solid #000;
		border-right: 1px solid #000;
		color: #cccccc;
		padding: 6px 10px; }

	button:hover, input[type='submit']:hover {
		background: #1e1e1e; }
	
	button:active, input[type='submit']:active {
		background: #1e1e1e; }
		
	a {
		text-decoration: none; }
	
	a:link, a:visited { 
		color: #6DBDF2; }

	a:active, a:hover { 
		color: #fff; }
</style>
</head>
<body>
<div id="container">
<h1>Blargboard XD install</h1>
<br>
<?php

function phpescape($var)
{
	$var = addslashes($var);
	$var = str_replace('\\\'', '\'', $var);
	return '"'.$var.'"';
}

function Shake($len=16)
{
	$cset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
	$salt = "";
	$chct = strlen($cset) - 1;
	while (strlen($salt) < $len)
		$salt .= $cset[mt_rand(0, $chct)];
	return $salt;
}

// Acmlmboard 1.x style
$footer = '</div></body></html>';

// pre-install checks

if (file_exists('config/database.php'))
	die('The board is already installed.'.$footer);
	
$footer = '<br><br><a href="javascript:window.history.back();">Go back and try again</a></div></body></html>';

if (version_compare(PHP_VERSION, '5.3.0') < 0)
	die('Error: Blargboard requires PHP 5.3 or above.'.$footer);
	
if (!is_dir('config'))
	if (!mkdir('config'))
		die('Error: failed to create the config directory. Check the permissions of the user running PHP.'.$footer);
	
@mkdir('templates_c');

if ($_POST['submit'])
{
	$boardusername = trim($_POST['boardusername']);
	$boardpassword = $_POST['boardpassword'];
	
	if (!$boardusername || !$boardpassword)
		die('Please enter a board username and password.'.$footer);
		
	if ($boardpassword !== $_POST['bpconfirm'])
		die('Error: the passwords you entered don\'t match.'.$footer);
	
	$test = new mysqli($_POST['dbserver'], $_POST['dbusername'], $_POST['dbpassword'], $_POST['dbname']);
	if ($test->connect_error)
		die('Error: failed to connect to the MySQL server: '.$test->connect_error.'<br><br>Check your parameters.'.$footer);
	
	$test->close();
	
	$dbconfig = 
'<?php
$dbserv = '.phpescape($_POST['dbserver']).';
$dbuser = '.phpescape($_POST['dbusername']).';
$dbpass = '.phpescape($_POST['dbpassword']).';
$dbname = '.phpescape($_POST['dbname']).';
$dbpref = '.phpescape($_POST['dbprefix']).';
$debugMode = 0;
$logSqlErrors = 0;
?>';
	if (file_put_contents('config/database.php', $dbconfig) === FALSE)
		die('Error: failed to create the config file. Check the permissions of the user running PHP.'.$footer);
	
	$salt = Shake(24);
	$saltfile = '<?php $salt = '.phpescape($salt).'; ?>';
	file_put_contents('config/salt.php', $saltfile);
	
	$kurifile = '<?php $kurikey = '.phpescape(Shake(32)).'; ?>';
	file_put_contents('config/kurikey.php', $kurifile);
	
	require('lib/mysql.php');
	require('lib/mysqlfunctions.php');
	$debugMode = 1;
	
	Upgrade();
	Import('database.sql');
	
	$pss = Shake(16);
	$sha = hash('sha256', $boardpassword.$salt.$pss, FALSE);
	
	Query("insert into {users} (id, name, password, pss, primarygroup, regdate, lastactivity, lastip, email, sex, theme) values ({0}, {1}, {2}, {3}, {4}, {5}, {5}, {6}, {7}, {8}, {9})", 
		1, $boardusername, $sha, $pss, 4, time(), $_SERVER['REMOTE_ADDR'], '', 2, 'blargboard');
		
?>
	<h3>Your new Blargboard XD board has been successfully installed!</h3>
	<br>
	You should now:
	<ul>
		<li>delete install.php and db/install.sql
		<li><a href="./login/">log in to your board</a> and configure it
	</ul>
	<br>
	Thank you for choosing Blargboard XD!<br>
	<br>
<?php
}
else
{
?>
	<form action="" method="POST">
	<div class="blarg">
	<table>
	
	<tr><td>MySQL server:</td><td><input type="text" name="dbserver" size=64 value="localhost"></td></tr>
	<tr><td>MySQL username:</td><td><input type="text" name="dbusername" size=64 value=""></td></tr>
	<tr><td>MySQL password:</td><td><input type="password" name="dbpassword" size=64 value=""></td></tr>
	<tr><td>MySQL database:</td><td><input type="text" name="dbname" size=64 value=""></td></tr>
	<tr><td colspan=2>&nbsp;</td></tr>
	
	<tr><td>Database table name prefix:</td><td><input type="text" name="dbprefix" size=64 value=""></td></tr>
	<tr><td colspan=2>This setting can be useful when the board's database is shared with other applications. If you're not sure what to put in there, leave it blank.</td></tr>
	<tr><td colspan=2>&nbsp;</td></tr>
	
	<tr><td>Board username:</td><td><input type="text" name="boardusername" size=64 maxlength=20 value=""></td></tr>
	<tr><td>Board password:</td><td><input type="password" name="boardpassword" size=64 value=""></td></tr>
	<tr><td>Confirm board password:</td><td><input type="password" name="bpconfirm" size=64 value=""></td></tr>
	<tr><td colspan=2>An owner account with these credentials will be created on your board after the install process has completed.</td></tr>
	<tr><td colspan=2>&nbsp;</td></tr>
	
	<tr><td colspan=2><input type="submit" name="submit" value="Install"></td></tr>
	
	</table>
	</div>
	</form>
<?php
}
?>
</div>
</body>
</html>
