<?php
//  AcmlmBoard XD - Login page
//  Access: guests
if (!defined('BLARG')) die();

if($_POST['action'] == "logout")
{
	setcookie("logsession", "", 2147483647, URL_ROOT, "", false, true);
	Query("UPDATE {users} SET loggedin = 0 WHERE id={0}", $loguserid);
	Query("DELETE FROM {sessions} WHERE id={0}", doHash($_COOKIE['logsession'].SALT));

	die(header("Location: ".URL_ROOT));
}
elseif(isset($_POST['actionlogin']))
{
	$okay = false;
	$pass = $_POST['pass'];

	$user = Fetch(Query("select * from {users} where name={0}", $_POST['name']));
	if($user)
	{
		$sha = doHash($pass.SALT.$user['pss']);
		if($user['password'] === $sha)
			$okay = true;
	}
	
	// auth plugins
	if (!$okay)
		{ $bucket = 'login'; include(BOARD_ROOT.'lib/pluginloader.php'); }

	if(!$okay)
	{
		Report("A visitor from [b]".$_SERVER['REMOTE_ADDR']."[/] tried to log in as [b]".$user['name']."[/].", 1);
		Alert(__("Invalid user name or password."));
	}
	else
	{
		//TODO: Tie sessions to IPs if user has enabled it (or probably not)

		$sessionID = Shake();
		setcookie("logsession", $sessionID, 2147483647, URL_ROOT, "", false, true);
		Query("INSERT INTO {sessions} (id, user, autoexpire) VALUES ({0}, {1}, {2})", doHash($sessionID.SALT), $user['id'], $_POST['session']?1:0);

		Report("[b]".$user['name']."[/] logged in.", 1);
		
		$rLogUser = Query("select id, pss, password from {users} where 1");
		$matches = array();

		while($testuser = Fetch($rLogUser))
		{
			if($testuser['id'] == $user['id'])
				continue;

			$sha = doHash($_POST['pass'].SALT.$testuser['pss']);
			if($testuser['password'] === $sha)
				$matches[] = $testuser['id'];
		}
		
		if (count($matches) > 0)
			Query("INSERT INTO {passmatches} (date,ip,user,matches) VALUES (UNIX_TIMESTAMP(),{0},{1},{2})", $_SERVER['REMOTE_ADDR'], $user['id'], implode(',',$matches));

		die(header("Location: ".URL_ROOT));
	}
}

$title = __('Log in');
MakeCrumbs(array('' => __('Log in')));

$forgotPass = '';

if(Settings::get("mailResetSender") != "")
	$forgotPass = "<button onclick=\"document.location = '".htmlentities(actionLink("lostpass"),ENT_QUOTES)."'; return false;\">".__("Forgot password?")."</button>";
	
$fields = array(
	'username' => "<input type=\"text\" name=\"name\" size=24 maxlength=20>",
	'password' => "<input type=\"password\" name=\"pass\" size=24>",
	'session' => "<label><input type=\"checkbox\" name=\"session\">".__("This session only")."</label>",
	
	'btnLogin' => "<input type=\"submit\" name=\"actionlogin\" value=\"".__("Log in")."\">",
	'btnForgotPass' => $forgotPass,
);

echo "<form name=\"loginform\" action=\"".htmlentities(actionLink("login"))."\" method=\"post\">";

RenderTemplate('form_login', array('fields' => $fields));

echo "</form>
	<script type=\"text/javascript\">
		document.loginform.name.focus();
	</script>";

?>
