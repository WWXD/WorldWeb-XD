<?php
//  WorldWeb XD - User account registration page
//  Access: Guests
//  Todo: Make it use templates
//	  - See bottom
if (!defined('BLARG')) die();

$title = __("Register");

$haveSecurimage = is_file(resourceLink('securimage/securimage.php')) && Settings::get('captcha') == "1" && !$loguser['root'];
$havebotdetect = is_file(resourceLink('lib/botdetect.php')) && Settings::get('captcha') == "2" && !$loguser['root'];
$RegisterWord = Settings::get('RegWordKey') !== "";
$Math = Settings::get('Math');

if ($haveSecurimage || $havebotdetect)
	session_start();

MakeCrumbs(['register' => __('Register')]);

$sexes = [__("Male"), __("Female"), __("N/A")];

if($loguserid && !$loguser['root'])
	Kill(__("An unknown error occured, please try again later."));
elseif($loguserid && $loguser['root'])
	Alert(__("You are currently logged in. However, you are a root, so you may re-register here freely"));

if(Settings::get('DisReg') && !$loguser['root'])
	Kill(__("Registering is currently disabled. Please try again later."));
else if(Settings::get('DisReg') && $loguser['root'])
	Alert(__("Registering is currently disabled, but you are a root."));

if($http->post('register')) {
	$err = "";
	if (IsProxy() && !$loguser['root']) {
		$adminemail = Settings::get('ownerEmail');

		if ($adminemail)
			$halp = '<br/><br/>If you aren\'t using a proxy, contact the board owner at: '.$adminemail;
		else
			$halp = '';

		$err .= 'Registrations from proxies are not allowed. Turn off your proxy and try again.'.$halp;
	} else {
		$name = trim($http->post('name'));
		$cname = str_replace(" ","", strtolower($name));

		$email = trim($http->post('email'));
		$cemail = str_replace(" ","", strtolower($email));

		$rUsers = Query("select name, displayname, email from {users}");
		while($user = Fetch($rUsers)) {
			$uname = trim(str_replace(" ", "", strtolower($user['name'])));
			if($uname == $cname)
				break;
			$uname = trim(str_replace(" ", "", strtolower($user['displayname'])));
			if($uname == $cname)
				break;

			$uemail = trim(str_replace(" ", "", strtolower($user['email'])));
			if($uemail == $cemail)
				break;
		}

		$rLoginBans = Query("select name from {loginbans}");
		while($LoginBans = Fetch($rLoginBans)) {
			$uban = trim(str_replace(" ", "", strtolower($LoginBans['name'])));
			if($uban == $cname)
				break;
		}

		$ipKnown = FetchResult("select COUNT(*) from {users} where lastip={0}", $_SERVER['REMOTE_ADDR']);

		//This makes testing faster.
		if($_SERVER['REMOTE_ADDR'] == "127.0.0.1" || $loguser['root'])
			$ipKnown = 0;

		if (stripos(in_array($cemail, $emaildomainsblock)) !== FALSE)
			$err .= '<ul>An unknown error occured, please try again.</ul>';
		if (!$cname)
			$err .= '<ul>Enter a username and try again.</ul>';
		if ($uname == $cname)
			$err .= "<ul>This user name is already taken by someone else. Please choose another one.</ul>";
		if ($uban == $cname)
			$err .= "<ul>This user name is not allowed to be used. Please choose another one.</ul>";
		if ($ipKnown >= 1)
			$err .= "<ul>You already have an account.</ul>";
		if (!$http->post('readFaq'))
			$err .= format("<ul>You really should {0}read the FAQ{1}&hellip;</ul>", "<a href=\"".actionLink("faq")."\">", "</a>");
		if (!$http->post('pass')) //Yes, I know that it should be common sence that you need to enter a password and that the "Less than 8 characters" thing exist, but its actually better to show the user what he's doing wrong. Other than to act blind to him.
			$err .= "<ul>You need to enter a password, for security reasons.</ul>";
		if (strlen($http->post('pass')) < 8 && $http->post('pass') !== "")
			$err .= "<ul>Your password must be at least eight characters long.</ul>";
		if ($http->post('pass') !== $http->post('pass2'))
			$err .= "<ul>The passwords you entered don't match.</ul>";
		if (!$http->post('pass2')) //I don't know if this is actually checked before. If a message already exists, please notify me.
			$err .= "<ul>You need to enter your password again.</ul>";
		if (!$cemail && Settings::get('emailVerification'))
			$err .= "<ul>You forgot to specify an email.</ul>";
		if ($http->post('botprot'))
			$err .= "<ul>An unknown error occured, please try again.";
		if ($uemail == $cemail)
			$err .= "<ul>You already have an account.</ul>";
		if (!filter_var($cemail, FILTER_VALIDATE_EMAIL))
			$err .= "<ul>You didn't imput your email correctly.</ul>";
		if ($http->post('pass') === $cname)
			$err .= "<ul>Don't put your username as your password. You'll impose high security risk to your account.</ul>";
		if (!$http->post('math') && Settings::get('math'))
			$err .= "<ul>You forgot to answer the math question.</ul>";
		if ($http->post('math') !== "11")
			$err .= "<ul>You got the Math answer wrong.</ul>";
		if (!$http->post('KeyWord') && Settings::get("RegWordKey") !== "")
			$err .= "<ul>You forgot to enter the Registration Word Key. Remeber that you have to contact the admin, in order to recieve it.</ul>";
		if ($http->post('KeyWord') !== Settings::get("RegWordKey"))
			$err .= "<ul>You entered the wrong registration key. Remember that you have to obtain this key from a admin.</ul>";
		if (strlen($cname)>20)
			$err .= "<ul>The maximum limit for usernames are 20 characters.</ul>";

		if($haveSecurimage) {
			include("securimage/securimage.php");
			$securimage = new Securimage();
			if($securimage->check($http->post('captcha_code')) == false)
				$err .= "<ul>You got the CAPTCHA wrong.</ul>";
		} else if($havebotdetect) {
			require("lib/botdetect.php");
			$isHuman = $ExampleCaptcha->Validate();
			if(!$isHuman)
				$err .= "<ul>You got the CAPTCHA wrong.</ul>";
		}
	}

	if($err !== "")
		Alert(__('There are some few errors with your registration field.<br/><ol>'.$err.'</ol><br/>Please fix all of these errors, and try again.'), __('Error'));
	else {
		$newsalt = Shake();
		$password = password_hash($http->post('pass'), PASSWORD_DEFAULT);
		$uid = FetchResult("SELECT id+1 FROM {users} WHERE (SELECT COUNT(*) FROM {users} u2 WHERE u2.id={users}.id+1)=0 ORDER BY id ASC LIMIT 1");
		if($uid < 2) $uid = 2;

		if (!Settings::Get('AdminVer')) {
			$rUsers = Query("insert into {users} (id, name, password, pss, primarygroup, regdate, lastactivity, lastip, email, sex, theme) values ({0}, {1}, {2}, {3}, {4}, {5}, {5}, {6}, {7}, {8}, {9})",
				$uid, $http->post('name'), $password, $newsalt, Settings::get('defaultGroup'), time(), $_SERVER['REMOTE_ADDR'], $http->post('email'), (int)$http->post('sex'), Settings::get("defaultTheme"));
		} else {
			//Todo: Add a title entry that says "Need Verification".
			//(Maybe) Make a new rank
			//Send a PM to all staff members notifying that he registered.
			$rUsers = Query("insert into {users} (id, name, password, pss, primarygroup, regdate, lastactivity, lastip, email, sex, theme) values ({0}, {1}, {2}, {3}, {4}, {5}, {5}, {6}, {7}, {8}, {9})",
				$uid, $http->post('name'), $password, $newsalt, Settings::get('bannedGroup'), time(), $_SERVER['REMOTE_ADDR'], $http->post('email'), (int)$http->post('sex'), Settings::get("defaultTheme"));
		}

		Report("New user: [b]".$http->post('name')."[/b] (#".$uid.") -> [g]#HERE#?uid=".$uid);

		$user = Fetch(Query("select * from {users} where id={0}", $uid));
		$user['rawpass'] = $http->post('pass');

		$bucket = "newuser"; include(BOARD_ROOT."lib/pluginloader.php");


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
				} else if($testuser['password'] === $pass) {
					$password = password_hash($pass, PASSWORD_DEFAULT);

					Query("UPDATE {users} SET password = {0} WHERE id={1}", $password, $testuser['id']);
					$matches[] = $testuser['id'];
				}
			}
		}

		if (count($matches) > 0)
			Query("INSERT INTO {passmatches} (date,ip,user,matches) VALUES (UNIX_TIMESTAMP(),{0},{1},{2})", $_SERVER['REMOTE_ADDR'], $user['id'], implode(',',$matches));

		// mark threads older than 15min as read
		Query("INSERT INTO {threadsread} (id,thread,date) SELECT {0}, id, {1} FROM {threads} WHERE lastpostdate<={2} ON DUPLICATE KEY UPDATE date={1}", $uid, time(), time()-900);


		if($http->post('autologin')) {
			$sessionID = Shake();
			setcookie("logsession", $sessionID, 0, URL_ROOT, "", false, true);
			Query("INSERT INTO {sessions} (id, user, autoexpire) VALUES ({0}, {1}, {2})", doHash($sessionID.SALT), $user['id'], 0);
			die(header("Location: ".actionLink('profile', $user['id'], '', $user['name'])));
		} else
			die(header("Location: ".pageLink("login")));
	}
} else {
	$http->post('name') = '';
	$http->post('email') = '';
	$http->post('sex') = 2;
	$http->post('autologin') = 0;
}

if(Settings::get('PassChecker')) {
	print "<script src=\"".resourceLink('js/register.js')."\"></script>
			<script src=\"".resourceLink('js/zxcvbn.js')."\"></script>";
}

if($havebotdetect) {
	print "<link type=\"text/css\" rel=\"Stylesheet\" href=".CaptchaUrls::LayoutStylesheetUrl()." />";
}

print "<form action=\"".htmlentities(pageLink("register"))."\" method=\"post\" onsubmit=\"register.disabled = true; return true;\">
	<table class=\"outline margin form form_register\">
		<tr class=\"header1\">
			<th colspan=\"2\">
				".__("Register")."
			</th>
		</tr>
		<tr>
			<td class=\"cell2 center\" style=\"width:20%;\">
				<label for=\"un\">".__("User name")."</label>
			</td>
			<td class=\"cell0\">
				<input type=\"text\" id=\"un\" name=\"name\" maxlength=20 size=24 autocorrect=off autocapitalize=words value=\"".htmlspecialchars($http->post('name'))."\" class=\"required\">
			</td>
		</tr>
		<tr>
			<td class=\"cell2 center\">
				<label for=\"pw\">".__("Password")."</label> <br>
				<small>Preferibly use passwords with at least 8 characters, with uppercase and lowercase letters, numbers and symbols.</small>
			</td>
			<td class=\"cell1\">
				<input type=\"password\" id=\"pw\" name=\"pass\" size=24 class=\"required\"> | Confirm: <input type=\"password\" id=\"pw2\" name=\"pass2\" size=24 class=\"required\">";
if(Settings::get('PassChecker'))
	print "<br><a href=\"javascript:void(0)\" onclick=\"create_password();\"><button>Generate Password</button></a>: <noscript>Sorry, but Javascript is required for this function to work</noscript> <span id=\"password\"></span>";
print "
				</td>
		</tr>
		<tr>
			<td class=\"cell2 center\">
				Email address
			</td>
			<td class=\"cell0\">
				<input type=\"email\" id=\"email\" type=email name=\"email\" value=\"".htmlspecialchars($http->post('email'))."\" maxlength=\"60\" size=24";
if (Settings::get('emailVerification'))
	print "class=\"required\"";
print "
				>
			</td>
		</tr>
		<tr>
			<td class=\"cell2 center\">
				Gender
			</td>
			<td class=\"cell1\">
				".MakeOptions("sex",$http->post('sex'),$sexes)."
			</td>
		</tr>
		<tr style=\"display:none;\">
			<td class=\"cell2 center\">
				Bot Protection
			</td>
			<td class=\"cell1\">
				<input type=\"text\" id=\"botprot\" name=\"botprot\">
			</td>
		</tr>";

if($haveSecurimage) {
	print "
		<tr>
			<td class=\"cell2\">
				".__("Captcha")."
			</td>
			<td class=\"cell1\">
				<img width=\"200\" height=\"80\" id=\"captcha\" src=\"".actionLink("captcha", shake())."\" alt=\"CAPTCHA Image\" />
				<button onclick=\"document.getElementById('captcha').src = '".actionLink("captcha", shake())."?' + Math.random(); return false;\">".__("New")."</button><br />
				<input type=\"text\" name=\"captcha_code\" size=\"10\" maxlength=\"6\" class=\"required\" />
			</td>
		</tr>";
} else if($havebotdetect) {
		print "
		<tr>
			<td class=\"cell2\">
				".__("Captcha")."
			</td>
			<td class=\"cell1\">";

			$ExampleCaptcha = new Captcha("ExampleCaptcha");
			$ExampleCaptcha->UserInputID = "CaptchaCode";
			echo $ExampleCaptcha->Html(); 

			print "
				<input name=\"CaptchaCode\" id=\"CaptchaCode\" type=\"text\" />
			</td>
		</tr>";
}

if(isset($math)) {
	print "
		<tr>
			<td class=\"cell2\">
				".__("Math question")."
			</td>
			<td class=\"cell1\">
				What's 9+10-8?
				<input type=\"text\" id=\"math\" name=\"math\" class=\"required\">
			</td>
		</tr>";
}

if($RegisterWord) {
	print "
		<tr>
			<td class=\"cell2\">
				".__("Registration Key")."
				<br><span class=\"smallFonts\">Contact an admin to request this key.<br>
									It's <b>NOT</b> a guarantee that you'll receive it.</span>
			</td>
			<td class=\"cell1\">
				<input type=\"text\" id=\"KeyWord\" name=\"KeyWord\" class=\"required\">
			</td>
		</tr>";
}

print "
		<tr>
			<td class=\"cell2\"></td>
			<td class=\"cell0\">
				<label><input type=\"checkbox\" name=\"readFaq\"> ".format(__("I have read the {0}FAQ{1}"), "<a href=\"".actionLink("faq")."\">", "</a>")."</label>
			</td>
		</tr>
		<tr class=\"cell2\">
			<td></td>
			<td>
				<input type=\"submit\" name=\"register\" value=\"".__("Register")."\">
				<label><input type=\"checkbox\" checked=\"checked\" name=\"autologin\"".($http->post('autologin')?' checked="checked"':'').">".__("Log in afterwards")."</label>
			</td>
		</tr>
		<tr>
			<td colspan=\"2\" class=\"cell0 smallFonts\" style=\"padding:0.7em;\">";

if (Settings::get('emailVerification'))
	print "Specifying an email address is a requirement. By default, your email is made private. You can change this setting later in the \"edit profile\" page if you desire to do so.";
else
	print "Specifying an email address isn't a requirement, but is recommended. By default, your email is made private. You can change this setting later in the \"edit profile\" page if you desire to do so.";

print "		</td>
		</tr>
		<tr>
			<td colspan=\"2\" class=\"cell1 smallFonts\" style=\"padding:0.7em;\">
				Do you already have an account? You should <a href=\"".pageLink("login")."\">log into it</a>, instead of making a new one. If you want to change your name, just ask the administrators to do it for you.
			</td>
		</tr>
	</table>";


function MakeOptions($fieldName, $checkedIndex, $choicesList) {
	$checks[$checkedIndex] = " checked=\"checked\"";
	$result = '';
	foreach($choicesList as $key=>$val)
		$result .= format("
					<label>
						<input type=\"radio\" name=\"{1}\" value=\"{0}\"{2}>
						{3}
					</label>", $key, $fieldName, (isset($checks[$key]) ? $checks[$key] : ''), $val);
	return $result;
}

function isValidPassword($password, $hash, $uid) {
	if (!password_verify($password, $hash))
		return false;

	if (password_needs_rehash($hash, PASSWORD_DEFAULT)) {
		$hash = password_hash($password, PASSWORD_DEFAULT);

		Query('UPDATE {users} SET password = {0} WHERE id = {1}', $hash, $uid);
	}

	return true;
}

function IsProxy() {
	if ($_SERVER['HTTP_X_FORWARDED_FOR'] && $_SERVER['HTTP_X_FORWARDED_FOR'] != $_SERVER['REMOTE_ADDR'])
		return true;

	//SFS checker
	$page = file_get_contents('http://api.stopforumspam.org/api?ip='.$_SERVER['REMOTE_ADDR'].'&json&notorexit');
	$a = json_decode($page);

	if($a->ip->torexit == 1)
		return true;

	return false;
}
