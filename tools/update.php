<!doctype html>
<html>
<head>
<title>Blargboard updater</title>
<style type="text/css">
a:link { color: #0e5; }
a:visited { color: #0e5; }
a:hover, a:active { color: #bfb; }

html, body { width: 100%; height: 100%; }

body
{
	font: 12pt 'Arial', 'Helvetica', sans-serif;
	background: #222;
	margin: 0;
	padding: 0;
	text-align: center;
	color: #fff;
}

#container
{
	background: #032;
	min-height: 100%;
	box-sizing: border-box;
	-moz-box-sizing: border-box;
	max-width: 1000px;
	margin: 0 auto;
	padding-top: 20px;
	padding-bottom: 0;
}

h1, h3
{
	border-bottom: 2px solid #0d5;
	padding-bottom: 1em;
}

input, select
{
	background: black;
	color: white;
	border: 1px solid #054;
}

input[type='submit'], input[type='button']
{
	border: 2px ridge #0d5;
}

.blarg
{
	margin: 1em;
}

table
{
	width: 100%;
	border-collapse: collapse;
}

td:not([colspan='2'])
{
	border-bottom: 1px solid #043;
}
</style>
</head>
<body>
<div id="container">
<h1>Blargboard updater</h1>
<br>
<?php

define('BLARG', 1);

// Acmlmboard 1.x style
$footer = '</div></body></html>';

if ($_POST['submit'])
{
	$boardusername = trim($_POST['boardusername']);
	$boardpassword = $_POST['boardpassword'];
	
	if (!$boardusername || !$boardpassword)
		die('Please enter a username and password.'.$footer);
	
	require(__DIR__.'/../config/salt.php');
	require(__DIR__.'/../lib/mysql.php');
	require(__DIR__.'/../db/functions.php');
	$debugMode = 1;
	
	$rootgroup = FetchResult("SELECT value FROM {settings} WHERE plugin={0} AND name={1}", 'main', 'rootGroup');
	$res = Query("SELECT password,pss FROM {users} WHERE (name = {0} OR displayname = {0}) AND primarygroup={1}", 
		$boardusername, $rootgroup);
		
	if (!NumRows($res)) die('Invalid username or password.'.$footer);
	$user = Fetch($res);
	if (hash('sha256', $boardpassword.SALT.$user['pss'], FALSE) !== $user['password'])
		die('Invalid username or password.'.$footer);
	
	Upgrade();
		
?>
	<h3>Your board has been updated!</h3>
<?php
}
else
{
?>
	<form action="" method="POST">
	<div class="blarg">
	<table>
	
	<tr><td colspan=2>This will update your board's database structure.</td></tr>
	<tr><td colspan=2>&nbsp;</td></tr>
	
	<tr><td colspan=2>Only board owners are allowed to perform this action. Enter your username and password to continue.</td></tr>
	<tr><td colspan=2>&nbsp;</td></tr>
	
	<tr><td>Username:</td><td><input type="text" name="boardusername" size=64 maxlength=20 value=""></td></tr>
	<tr><td>Password:</td><td><input type="password" name="boardpassword" size=64 value=""></td></tr>
	<tr><td colspan=2>&nbsp;</td></tr>
	
	<tr><td colspan=2><input type="submit" name="submit" value="Update"></td></tr>
	
	</table>
	</div>
	</form>
<?php
}
?>
</div>
</body>
</html>