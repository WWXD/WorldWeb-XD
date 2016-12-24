<!doctype html>
<html>
	<head>
		<title>Worldweb XD Installation</title>
		<style>
			@import url(http://fonts.googleapis.com/css?family=Roboto);
			@import url(http://fonts.googleapis.com/css?family=Roboto+Slab);

			html, body { width: 100%; height: 100%; }

			body {
				background: #0d0d0d;
				font-family: 'Roboto', sans-serif;
				color: #dedede;
			}

			.container {
				max-width: 1366px;
				margin-left: auto;
				margin-right: auto;
			}

			.outline {
				margin-bottom: 15px;
			}

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
				background: #202020; }

			.col2 {
				background: rgb(30,30,30);
				background: -moz-linear-gradient(top, rgba(30,30,30,1) 0%, rgba(20,20,20,1) 100%);
				background: -webkit-linear-gradient(top, rgba(30,30,30,1) 0%,rgba(20,20,20,1) 100%);
				background: linear-gradient(to bottom, rgba(30,30,30,1) 0%,rgba(20,20,20,1) 100%); }

			.center {
				text-align: center; }

			#title {
				font-size: 5em;
				color: #fff;
				margin-top: 5px;
				text-align: center;
				font-family: 'Roboto Slab'; 
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
		</style>
	</head>
	<body>
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

	define('BLARG', 1);

	$header = '<div class="container"><div class="outline"><div class="box header center">Error</div><div class="box cell center">';
	$footer = '</div></div></div></body></html>';

	if (file_exists(__DIR__.'/config/database.php'))
		die($header.'The website is already installed.'.$footer);
		
	$footer = '<br><br><a href="javascript:window.history.back();">Go back and try again</a></div></div></div></body></html>';

	if (version_compare(PHP_VERSION, '5.3.0') < 0)
		die($header.'Sorry, Blargboard XD requires PHP 5.3 or above.'.$footer);
		
	if (!is_dir(__DIR__.'/config'))
		if (!mkdir(__DIR__.'/config'))
			die($header.'Failed to create the config directory. Check the permissions of the user running PHP.'.$footer);
		
	@mkdir(__DIR__.'/templates_c');

	if ($_POST['submit']) {
		$boardusername = trim($_POST['boardusername']);
		$boardpassword = $_POST['boardpassword'];

		if (!$boardusername || !$boardpassword)
			die($header.'Please enter a admin username and password.'.$footer);

		if ($boardpassword !== $_POST['bpconfirm'])
			die($header.'The passwords you entered don\'t match.'.$footer);

		$test = new mysqli($_POST['dbserver'], $_POST['dbusername'], $_POST['dbpassword'], $_POST['dbname']);
		if ($test->connect_error)
			die($header.'Failed to connect to the MySQL server: '.$test->connect_error.'<br><br>Check your parameters.'.$footer);

		$test->close();

		$dbconfig =  '<?php
		$dbserv = '.phpescape($_POST['dbserver']).';
		$dbuser = '.phpescape($_POST['dbusername']).';
		$dbpass = '.phpescape($_POST['dbpassword']).';
		$dbname = '.phpescape($_POST['dbname']).';
		$dbpref = '.phpescape($_POST['dbprefix']).';
		$debugMode = 0;
		$logSqlErrors = 0;
		?>';

		if (file_put_contents(__DIR__.'/config/database.php', $dbconfig) === FALSE)
			die($header.'Failed to create the config file. Check the permissions of the user running PHP.'.$footer);

		$salt = Shake(24);
		define('SALT', $salt);
		$saltfile = '<?php define(\'SALT\', '.phpescape($salt).'); ?>';
		file_put_contents(__DIR__.'/config/salt.php', $saltfile);

		$kurifile = '<?php define(\'KURIKEY\', '.phpescape(Shake(32)).'); ?>';
		file_put_contents(__DIR__.'/config/kurikey.php', $kurifile);

		require(__DIR__.'/lib/mysql.php');
		require(__DIR__.'/db/functions.php');
		$debugMode = 1;

		echo '<div class="container"><div class="outline"><div class="box header center">Installing...</div><div class="box cell center">';

		Upgrade();
		Import(__DIR__.'/db/install.sql');

		$pss = Shake(16);
		$sha = hash('sha256', $boardpassword.$salt.$pss, FALSE);

		Query("insert into {users} (id, name, password, pss, primarygroup, regdate, lastactivity, lastip, email, sex, theme) values ({0}, {1}, {2}, {3}, {4}, {5}, {5}, {6}, {7}, {8}, {9})", 
			1, $boardusername, $sha, $pss, 4, time(), $_SERVER['REMOTE_ADDR'], '', 2, 'blargboard');

		echo '</div></div></div>';

	?>
		<div class="container">
			<div class="outline">
				<div class="box header center">
					Congratulations!
				</div>
				<div class="box cell center">
					The WorldWeb XD installation was successful. You may now <a href="./login/">proceed to your website and login</a>. Make sure you edit the website's setting. Thanks for choosing Blargboard XD!
				</div>
			</div>
		</div>
	<?php
		unlink(__DIR__.'/db/install.sql');
		unlink(__DIR__.'/install.php');
	} else {
	?>
		<div class="container">
			<div class="outline">
				<div class="box cell">
					<div id="title">
						WorldWeb XD Installer
					</div>
				</div>
				<div class="box col2 center">
					<a href="https://github.com/WorldWeb-XD/WorldWeb-XD">GitHub repo</a> - <a href="https://github.com/WorldWeb-XD/WorldWeb-XD/blob/master/README.md">Readme file</a>
				</div>
			</div>
			<div class="outline">
				<div class="box cell center">
					Please note that you are using a version of WorldWeb XD obtained from the Git repository.<br />
-					This version of WorldWeb XD may have <em>serious vulnerabilites</em> and (some features) might <em>not work at all</em>.
				</div>
			</div>
				<form action="" method="POST">
					<div class="outline">
						<div class="box col2 center">
							MySQL Parameters
						</div>
						<div class="box cell center">
							MySQL Server: <input type="text" name="dbserver" size=30 value="localhost">
						</div>
						<div class="box cell center">
							MySQL Username: <input type="text" name="dbusername" size=30 value="">
						</div>
						<div class="box cell center">
							MySQL Password: <input type="password" name="dbpassword" size=30 value="">
						</div>
						<div class="box cell center">
							MySQL Database: <input type="text" name="dbname" size=30 value="">
						</div>
						<div class="box cell center">
							Table Prefix: <input type="text" name="dbprefix" size=30 value=""><br>
							<small>Change this if the websites's database is shared with other applications. Leaving this blank is fine.</small>
						</div>
						<div class="box col2 center">
							Owner Credentials
						</div>
						<div class="box cell center">
							Username: <input type="text" name="boardusername" size=30 maxlength=20 value="">
						</div>
						<div class="box cell center">
							Password: <input type="password" name="boardpassword" size=30 value="">
						</div>
						<div class="box cell center">
							Confirm Password: <input type="password" name="bpconfirm" size=30 value=""><br>
						<small>Once the installation is complete, the owner account with these credentials will be made. Sign into that when you're done with the installation proccess.</small>
						</div>
						<div class="box cell center">
							<input type="submit" name="submit" value="Install WorldWeb XD">
						</div>
					</div>
				</form>
			</div>
		</div>
	<?php
	}
	?>
	</body>
</html>
