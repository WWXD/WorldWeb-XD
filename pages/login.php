<?php
//  AcmlmBoard XD - Login page
//  Access: guests
if (!defined('BLARG')) die();

// This is needed to keep up to date with new hashing settings.
// From https://gist.github.com/nikic/3707231#rehashing-passwords
function isValidPassword($password, $hash, $uid) {
	if (!password_verify($password, $hash))
		return false;

	if (password_needs_rehash($hash, PASSWORD_DEFAULT)) {
		$hash = password_hash($password, PASSWORD_DEFAULT);

		Query('UPDATE {users} SET password = {0} WHERE id = {1}', $hash, $uid);
	}

	return true;
}

if($http->post('action') === "logout" && $loguserid) {
	setcookie("logsession", "", 2147483647, URL_ROOT, "", false, true);
	Query("UPDATE {users} SET loggedin = 0 WHERE id={0}", $loguserid);
	Query("DELETE FROM {sessions} WHERE id={0}", doHash($_COOKIE['logsession'].SALT));

	die(header("Location: ".URL_ROOT));
} elseif($http->post('action') === "login" && $loguserid) {
	Kill(__("Your already logged in. First log out."));
} elseif($http->post('action') === "logout" && !$loguserid) {
	Kill(__("Why in the world are you trying to log out if your not even logged in?"));
} elseif($http->post('action') === "login" && !$loguserid) {
	$okay = false;
	$pass = $http->post('pass');

	$user = Fetch(Query("select * from {users} where name={0} or email={0}", $http->post('name')));
	if($user) {
		// Check for the password. (new type)
		if (isValidPassword($pass, $user['password'], $user['id']))
			$okay = true;
		else {
			// Check for the legacy ABXD password and convert it to the new password.
			$sha = doHash($pass.$salt.$user['pss']);
			if ($user['password'] == $sha) {
				$password = password_hash($pass, PASSWORD_DEFAULT);

				Query("UPDATE {users} SET password = {0} WHERE id={1}", $password, $user['id']);
				$okay = true;
			} else if($user['password'] === $pass) {
				$password = password_hash($pass, PASSWORD_DEFAULT);

				Query("UPDATE {users} SET password = {0} WHERE id={1}", $password, $user['id']);
				$okay = true;
			} 
		}
	}

	// auth plugins

	if(!$okay) {
		Report("A visitor from [b]".$_SERVER['REMOTE_ADDR']."[/] tried to log in as [b]".$http->post('name')."[/].", 1);
		Alert(__("Invalid user name or password."));
		$bucket = 'login'; include(BOARD_ROOT.'lib/pluginloader.php');
	} else {
		//TODO: Tie sessions to IPs if user has enabled it (or probably not)

		$sessionID = Shake();
		setcookie("logsession", $sessionID, 2147483647, URL_ROOT, "", false, true);
		Query("INSERT INTO {sessions} (id, user, autoexpire) VALUES ({0}, {1}, {2})", doHash($sessionID.SALT), $user['id'], $http->post('session')?1:0);

		Report("[b]".$user['name']."[/] logged in from [b]".$_SERVER['REMOTE_ADDR']."[/].", 1);

		$rLogUser = Query("select id, pss, password from {users} where 1");
		$matches = [];

		while($testuser = Fetch($rLogUser)) {
			if($testuser['id'] == $user['id'])
				continue;

			if (isValidPassword($pass, $testuser['password'], $testuser['id']))
				$matches[] = $testuser['id'];
			else {
				$sha = doHash($http->post('pass').SALT.$testuser['pss']);
				if($testuser['password'] === $sha) {
					$password = password_hash($pass, PASSWORD_DEFAULT);

					Query("UPDATE {users} SET password = {0} WHERE id={1}", $password, $testuser['id']);
					$matches[] = $testuser['id'];
				}
			}
		}

		if (count($matches) > 0)
			Query("INSERT INTO {passmatches} (date,ip,user,matches) VALUES (UNIX_TIMESTAMP(),{0},{1},{2})", $_SERVER['REMOTE_ADDR'], $user['id'], implode(',',$matches));

		die(header("Location: ".URL_ROOT));
	}
}

$title = __('Log in');
MakeCrumbs(['' => __('Log in')]);

$forgotPass = '';

if(Settings::get("mailResetSender") != "")
	$forgotPass = "<button onclick=\"document.location = '".htmlentities(pageLink("lostpass"),ENT_QUOTES)."'; return false;\">".__("Forgot password?")."</button>";

$fields = [
	'username' => "<input type=\"text\" name=\"name\" size=24 maxlength=50>",
	'password' => "<input type=\"password\" name=\"pass\" size=24>",
	'session' => "<label><input type=\"checkbox\" name=\"session\">".__("This session only")."</label>",

	'btnLogin' => "<input type=\"submit\" name=\"actionlogin\" value=\"".__("Log in")."\">",
	'btnForgotPass' => $forgotPass,
];

echo "<form name=\"loginform\" action=\"".htmlentities(pageLink("login"))."\" method=\"post\" onsubmit=\"actionlogin.disabled = true; return true;\">";

RenderTemplate('form_login', ['fields' => $fields]);

echo "</form>
	<script type=\"text/javascript\">
		document.loginform.name.focus();
	</script>";