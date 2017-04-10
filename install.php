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
				padding: 5px;
			}

			.header {
				background: #1a1a1a;
				padding: 4px 8px;
			}

			.cell {
				background: #202020; }

			.col2 {
				background: rgb(30,30,30);
				background: -moz-linear-gradient(top, rgba(30,30,30,1) 0%, rgba(20,20,20,1) 100%);
				background: -webkit-linear-gradient(top, rgba(30,30,30,1) 0%,rgba(20,20,20,1) 100%);
				background: linear-gradient(to bottom, rgba(30,30,30,1) 0%,rgba(20,20,20,1) 100%);
			}

			.center {
				text-align: center; }

			.left {
				text-align: left; }

			#title {
				font-size: 5em;
				color: #fff;
				margin-top: 5px;
				text-align: center;
				font-family: 'Roboto Slab'; 
				font-weight: normal;
			}

			textarea, input {
				color: #eeeeee; }

			textarea, input[type='text'], input[type='password'], input[type='email'] {
				border: 1px solid #555555;
				background: #333333;
				padding: 4px
			}

			textarea:hover, input[type='text']:hover, input[type='password']:hover, input[type='email']:hover {
				border: 1px solid #888; }

			textarea:focus, textarea:active, input[type='text']:focus, input[type='text']:active, input[type='password']:focus, input[type='password']:active, input[type='email']:focus, 	input[type='email']:active {
				border: 1px solid #999;
				background: #484848;
			}

			button, input[type='submit'] {
				background: #1a1a1a;
				border-top: 1px solid #383838;
				border-left: 1px solid #383838;
				border-bottom: 1px solid #000;
				border-right: 1px solid #000;
				color: #cccccc;
				padding: 6px 10px;
			}

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

	function phpescape($var) {
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

	function htmltrim__recursive($var, $level = 0) {
		// Remove spaces (32), tabs (9), returns (13, 10, and 11), nulls (0), and hard spaces. (160)
		if (!is_array($var))
			return trim($var, ' ' . "\t\n\r\x0B" . "\0" . "\xA0");

		// Go through all the elements and remove the whitespace.
		foreach ($var as $k => $v)
			$var[$k] = $level > 25 ? null : htmltrim__recursive($v, $level + 1);

		return $var;
	}
	
	if (!function_exists('password_hash'))
		require_once(__DIR__ . '/lib/password.php');

	foreach ($_POST as $key => $value) {
		if (!is_array($_POST[$key]))
			$_POST[$key] = htmltrim__recursive(str_replace(["\n", "\r"], '', $_POST[$key]));
	}

	define('BLARG', 1);

	$header = '<div class="container"><div class="outline"><div class="box header center">Error</div><div class="box cell center">';
	$footer = '</div></div></div></body></html>';

	if (file_exists(__DIR__.'/config/database.php'))
		die($header.'The website is already installed.'.$footer);

	if(ini_get('register_globals'))
		die($header."PHP, as it is running on this server, has the <code>register_globals</code> setting turned on. This is something of a security hazard, and is a <a href=\"http://en.wikipedia.org/wiki/Deprecation\" target=\"_blank\">deprecated function</a>. For more information on this topic, please refer to the <a href=\"http://php.net/manual/en/security.globals.php\" target=\"_blank\">PHP manual</a>.<br><br>At any rate, this is designed to run with <code>register_globals</code> turned <em>off</em>. You can try adding the line <code>php_flag register_globals off</code> to an <code>.htaccess</code> file on your website root directory (often something like <code>public_html</code>). If not, ask your provider to edit the <code>php.ini</code> file accordingly and make the internet a little safer for all of us.".$footer);

	if (!function_exists('version_compare') || version_compare(PHP_VERSION, '5.5', '=<'))
		die($header.'Sorry, WorldWeb XD requires PHP version 5.5 or above, while you are currently running '. PHP_VERSION .'. Please update to the latest, and <a href="javascript:window.history.back();">try again</a>.'.$footer);

	$footer = '<br><br><a href="javascript:window.history.back();">Go back and try again</a></div></div></div></body></html>';

	if (!is_dir(__DIR__.'/config')) {
		if (!mkdir(__DIR__.'/config')) {
			die($header.'Failed to create the config directory. Check the permissions of the user running PHP.'.$footer);
		}
	}

	//Make all the data folders...
	@mkdir(__DIR__.'/data');
	@mkdir(__DIR__.'/data/avatars');
	@mkdir(__DIR__.'/data/minipics');
	@mkdir(__DIR__.'/data/uploads');

	@mkdir(__DIR__.'/templates_c');

	if ($_POST['submit']) {
		$ownerusername = trim($_POST['ownerusername']);
		$ownerpassword = $_POST['ownerpassword'];
		$ownpassconf = $_POST['opconfirm'];

		if (!$ownerusername && !$ownerpassword)
			die($header.'Please enter a admin username and password.'.$footer);
		else if (!$ownerusername && $ownerpassword)
			die($header.'Please enter a admin username.'.$footer);
		else if ($ownerusername && (!$ownerpassword && !$ownpassconf))
			die($header.'Please enter a admin password.'.$footer);
		else if ((!$ownerpassword && $ownpassconf) || ($ownerpassword && !$ownpassconf))
			die($header.'Please enter a admin password twice.'.$footer);

		if ($ownerpassword !== $ownpassconf)
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

		require(__DIR__.'/lib/mysql.php');
		require(__DIR__.'/db/functions.php');
		$debugMode = 1;

		//4th page starts here
		echo '<div class="container" id="page3"><div class="outline"><div class="box header center">Installing...</div><div class="box cell center">';

		Upgrade();
		Import(__DIR__.'/db/install.sql');

		$newsalt = Shake();
		$sha = password_hash($ownerpassword, PASSWORD_DEFAULT);

		Query("insert into {users} (id, name, password, pss, primarygroup, regdate, lastactivity, lastip, email, sex, theme) values ({0}, {1}, {2}, {3}, {4}, {5}, {5}, {6}, {7}, {8}, {9})", 
			1, $ownerusername, $sha, $newsalt, 4, time(), $_SERVER['REMOTE_ADDR'], '', 2, 'blargboard');

		echo '</div></div></div>';
		//4th page ends here

	?>
		//5th page starts here
		<div class="container" id="page4">
			<div class="outline">
				<div class="box header center">
					Congratulations!
				</div>
				<div class="box cell center">
					The WorldWeb XD installation was successful. You may now <a href="./">proceed to your website</a> and <a href="./login/">login</a>. Make sure you edit the website's setting. Thanks for choosing WorldWeb XD!
				</div>
			</div>
		</div>
		//5th page ends here
	<?php
		unlink(__DIR__.'/db/install.sql');
		unlink(__DIR__.'/install.php');
//		unlink(__DIR__.'/config/database_sample.php'); I'm commenting this out until I get around to actually making that file
	} else {
	?>
		<div class="container">
			//Content that should be shown on every page starts here.
			<div class="outline">
				<div class="box cell">
					<div id="title">
						WorldWeb XD Installer
					</div>
				</div>
				<div class="box col2 center">
					<a href="https://github.com/WWXD/WorldWeb-XD/">Github repo</a> - <a href="https://github.com/WWXD/WorldWeb-XD/blob/master/README.md">Readme file</a>
				</div>
			</div>
			<div class="outline">
				<div class="box cell center">
					Please note that you are using a version of WorldWeb XD obtained from the Git repository.<br />
					This version of WorldWeb XD may have <em>serious vulnerabilites</em> and (some features) might <em>not work at all</em>.
				</div>
			</div>
			//Content that should be shown on every page ends here
			//1st page starts here.
			<div class="outline" id="page0">
				<div class="box cell center">
					Welcome to WorldWeb XD. Before getting started, we need some information on the database. You will need to know the following items before proceeding.
						<ol>
							<li>Database name</li>
							<li>Database username</li>
							<li>Database password</li>
							<li>Database host</li>
							<li>Table prefix (if the websites's database is shared with other applications)</li>
						</ol>
					<br/><br/>
					We’re going to use this information to create a <samp>database.php</samp> file. <b>If for any reason this automatic file creation doesn’t work, don’t worry. All this does is fill in the database information to a configuration file. You may also simply open <samp>database-sample.php</samp> in a text editor, fill in your information, and save it as <samp>database.php</samp>.</b>
				</div>
			</div>
				<form action="" method="POST">
					//second page starts here
					<div class="outline" id="page1">
						<div class="box col2 center">
							MySQL Parameters
						</div>
						<div class="box cell center">
							MySQL Server: <input type="text" name="dbserver" size="30" value="localhost">
						</div>
						<div class="box cell center">
							MySQL Username: <input type="text" name="dbusername" size="30" value="">
						</div>
						<div class="box cell center">
							MySQL Password: <input type="password" name="dbpassword" size="30" value="">
						</div>
						<div class="box cell center">
							MySQL Database: <input type="text" name="dbname" size="30" value="">
						</div>
						<div class="box cell center">
							Table Prefix: <input type="text" name="dbprefix" size="30" value=""><br>
							<small>Change this if the websites's database is shared with other applications. Leaving this blank is fine.</small>
						</div>
					</div>
					//Second page ends here
					//Third Page starts here
					<div class="outline" id="page2">
						<div class="box col2 center">
							Owner Credentials
						</div>
						<div class="box cell center">
							Owner Username: <input type="text" name="ownerusername" size=30 maxlength=20 value="">
						</div>
						<div class="box cell center">
							Owner Password: <input type="password" name="ownerpassword" size=30 value="">
						</div>
						<div class="box cell center">
							Confirm Password: <input type="password" name="opconfirm" size=30 value=""><br>
						<small>Once the installation is complete, the owner account with these credentials will be made. Sign into that when you're done with the installation proccess.</small>
						</div>
						<div class="box cell center">
							<input type="submit" name="submit" value="Install WorldWeb XD">
						</div>
					</div>
					//third page ends here.
				</form>
			</div>
		</div>
	<?php
	}
	?>
	</body>
</html>
