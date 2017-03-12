<?php
//  Blargboard XD - User account registration page
//  Access: Guests
//  Todo: Make it use templates
//      - See bottom
if (!defined('BLARG')) die();

$title = __("Register");

$haveSecurimage = is_file(resourceLink('securimage/securimage.php')) && Settings::get('captcha');
if($haveSecurimage)
	session_start();

MakeCrumbs(array('register' => __('Register')));

$sexes = array(__("Male"), __("Female"), __("N/A"));

if($loguserid && !$loguser['root'])
	Kill(__("An unknown error occured, please try again later."));

if(Settings::get('DisReg') && !$loguser['root'])
	Kill(__("Registering is currently disabled. Please try again later."));

if($_POST['register']) {
	if (IsProxy() || IsProxyFSpamList()) {
		$adminemail = Settings::get('ownerEmail');
		
		if ($adminemail)
			$halp = '<br/><br/>If you aren\'t using a proxy, contact the board owner at: '.$adminemail;
		else
			$halp = '';

		$err = __('Registrations from proxies are not allowed. Turn off your proxy and try again.'.$halp);
	} else {
		$name = trim($_POST['name']);
		$cname = str_replace(" ","", strtolower($name));

		$email = trim($_POST['email']);
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

		$ipKnown = FetchResult("select COUNT(*) from {users} where lastip={0}", $_SERVER['REMOTE_ADDR']);

		//This makes testing faster.
		if($_SERVER['REMOTE_ADDR'] == "127.0.0.1" || $loguser['root'])
			$ipKnown = 0;

		if (stripos(in_array($cemail, $emaildomainsblock)) !== FALSE)
			$err = __('An unknown error occured, please try again.');
		if (!$cname)
			$err = __('Enter a username and try again.');
		if ($uname == $cname)
			$err = __("This user name is already taken by someone else. Please choose another one.");
		if ($ipKnown >= 1)
			$err = __("You already have an account.");
		if (!$_POST['readFaq'])
			$err = format(__("You really should {0}read the FAQ{1}&hellip;"), "<a href=\"".actionLink("faq")."\">", "</a>");
		if (!$_POST['pass']) //Yes, I know that it should be common sence that you need to enter a password and that the "Less than 8 characters" thing exist, but its actually better to show the user what he's doing wrong. Other than to act blind to him.
			$err = __("You need to enter your password.");
		if (strlen($_POST['pass']) < 8)
			$err = __("Your password must be at least eight characters long.");
		if ($_POST['pass'] !== $_POST['pass2'])
			$err = __("The passwords you entered don't match.");
		if (!$_POST['pass2']) //I don't know if this is actually checked before. If a message already exists, please notify me.
			$err = __("You need to enter your password again.");
		if (!$cemail && Settings::get('email'))
			$err = __("You need to specify an email. Please specify one, and try again.");
		if ($_POST['botprot'])
			$err = __("An unknown error occured, please try again.");
		if ($uemail == $cemail)
			$err = __("You already have an account.");
		if (!filter_var($cemail, FILTER_VALIDATE_EMAIL))
			$err = __("An unknown error occured, please try again.");
		if (($_POST['pass'] || $_POST['pass2']) == $cname)
			$err = __("Don't put your username as your password. You'll impose high security risk to your account");
		if (!$_POST['math'] && Settings::get('math'))
			$err = __("You forgot to answer the math question.");
		if ($_POST['math'] !== "11")
			$err = __("Wrong Math Answer. Please try again.");
		if (!$_POST['KeyWord'] && Settings::get("RegWordKey") !== "")
			$err = __("You forgot to enter the Registration Word Key. Please try again. Remeber that you have to contact the admin, in order to recieve it.");
		if ($_POST['KeyWord'] !== Settings::get("RegWordKey"))
			$err = __("You entered the wrong registration key. Please try again, but this time, with the right Registration Key. Remember that you have to obtain this key from a admin.");
		if (strlen($cname)>20)
			$err = __("The maximum limit for usernames are 20 characters. Please try again, but this time, with a shorter username");

		if($haveSecurimage) {
			include("securimage/securimage.php");
			$securimage = new Securimage();
			if($securimage->check($_POST['captcha_code']) == false)
				$err = __("You got the CAPTCHA wrong.");
		}

		$reasons = array();
		if(IsTorExitPoint()) {
			$reasons[] = 'tor';
		}
		$s = new StopForumSpam($stopForumSpamKey);
		if($s->is_spammer(array('email' => $_POST['email'], 'ip' => $_SERVER['REMOTE_ADDR'] ))) {
			$reasons[] = 'sfs';
		}
		if(count($reasons)) {
			$reason = implode(',', $reasons);
			$bucket = "regfail"; include("lib/pluginloader.php");
			$err = 'An unknown error occured, please try again.';
		}
	}

	if($err)
		Alert($err, __('Error'));
	else {
		$newsalt = Shake();
		$password = password_hash($_POST['pass'], PASSWORD_DEFAULT);
		$uid = FetchResult("SELECT id+1 FROM {users} WHERE (SELECT COUNT(*) FROM {users} u2 WHERE u2.id={users}.id+1)=0 ORDER BY id ASC LIMIT 1");
		if($uid < 1) $uid = 1;

		if (!Settings::Get('AdminVer')) {
			$rUsers = Query("insert into {users} (id, name, password, pss, primarygroup, regdate, lastactivity, lastip, email, sex, theme) values ({0}, {1}, {2}, {3}, {4}, {5}, {5}, {6}, {7}, {8}, {9})", 
				$uid, $_POST['name'], $password, $newsalt, Settings::get('defaultGroup'), time(), $_SERVER['REMOTE_ADDR'], $_POST['email'], (int)$_POST['sex'], Settings::get("defaultTheme"));
		} else {
			//Todo: Add a title entry that says "Need Verification".
			//(Maybe) Make a new rank
			//Send a PM to all staff members notifying that he registered.
			$rUsers = Query("insert into {users} (id, name, password, pss, primarygroup, regdate, lastactivity, lastip, email, sex, theme) values ({0}, {1}, {2}, {3}, {4}, {5}, {5}, {6}, {7}, {8}, {9})", 
				$uid, $_POST['name'], $password, $newsalt, Settings::get('bannedGroup'), time(), $_SERVER['REMOTE_ADDR'], $_POST['email'], (int)$_POST['sex'], Settings::get("defaultTheme"));
		}

		Report("New user: [b]".$_POST['name']."[/] (#".$uid.") -> [g]#HERE#?uid=".$uid);

		$user = Fetch(Query("select * from {users} where id={0}", $uid));
		$user['rawpass'] = $_POST['pass'];

		$bucket = "newuser"; include(BOARD_ROOT."lib/pluginloader.php");


		$rLogUser = Query("select id, pss, password from {users} where 1");
		$matches = array();

		while($testuser = Fetch($rLogUser)) {
			if($testuser['id'] == $user['id'])
				continue;

			$sha = doHash($_POST['pass'].SALT.$testuser['pss']);
			if($testuser['password'] === $sha)
				$matches[] = $testuser['id'];
		}

		if (count($matches) > 0)
			Query("INSERT INTO {passmatches} (date,ip,user,matches) VALUES (UNIX_TIMESTAMP(),{0},{1},{2})", $_SERVER['REMOTE_ADDR'], $user['id'], implode(',',$matches));

		// mark threads older than 15min as read
		Query("INSERT INTO {threadsread} (id,thread,date) SELECT {0}, id, {1} FROM {threads} WHERE lastpostdate<={2} ON DUPLICATE KEY UPDATE date={1}", $uid, time(), time()-900);


		if($_POST['autologin']) {
			$sessionID = Shake();
			setcookie("logsession", $sessionID, 0, URL_ROOT, "", false, true);
			Query("INSERT INTO {sessions} (id, user, autoexpire) VALUES ({0}, {1}, {2})", doHash($sessionID.SALT), $user['id'], 0);
			die(header("Location: ".actionLink('profile', $user['id'], '', $user['name'])));
		} else
			die(header("Location: ".actionLink("login")));
	}
} else {
	$_POST['name'] = '';
	$_POST['email'] = '';
	$_POST['sex'] = 2;
	$_POST['autologin'] = 0;
}

if(Settings::get('PassChecker')) {
	print "<script src=\"".resourceLink('js/register.js')."\"></script>
			<script src=\"".resourceLink('js/zxcvbn.js')."\"></script>";
}

print "<form action=\"".htmlentities(actionLink("register"))."\" method=\"post\">
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
				<input type=\"text\" id=\"un\" name=\"name\" maxlength=20 size=24 autocorrect=off autocapitalize=words value=\"".htmlspecialchars($_POST['name'])."\" class=\"required\">
			</td>
		</tr>
		<tr>
			<td class=\"cell2 center\">
				<label for=\"pw\">".__("Password")."</label>
				<small>Preferibly use passwords with at least 8 characters, with uppercase and lowercase letters, numbers and symbols.</small>
			</td>
			<td class=\"cell1\">
				<input type=\"password\" id=\"pw\" name=\"pass\" size=24 class=\"required\"> | Confirm: <input type=\"password\" id=\"pw2\" name=\"pass2\" size=24 class=\"required\">
			</td>
		</tr>
		<tr>
			<td class=\"cell2 center\">
				Email address
			</td>
			<td class=\"cell0\">
				<input type=\"email\" id=\"email\" type=email name=\"email\" value=\"".htmlspecialchars($_POST['email'])."\" maxlength=\"60\" size=24";
if (Settings::get('email'))
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
				".MakeOptions("sex",$_POST['sex'],$sexes)."
			</td>
		</tr>
		<tr style=\"display:none;\">
			<td class=\"cell2 center\">
				Bot Protection
			</td>
			<td class=\"cell1\">
				<input type=\"text\" id=\"botprot\" name=\"botprot\" style=\"display: none;\">
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
}

if(Settings::get('math')) {
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

if(Settings::get('RegWordKey') !== "") {
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
				<label><input type=\"checkbox\" name=\"readFaq\">".format(__("I have read the {0}FAQ{1}"), "<a href=\"".actionLink("faq")."\">", "</a>")."</label>
			</td>
		</tr>
		<tr class=\"cell2\">
			<td></td>
			<td>
				<input type=\"submit\" name=\"register\" value=\"".__("Register")."\">
				<label><input type=\"checkbox\" checked=\"checked\" name=\"autologin\"".($_POST['autologin']?' checked="checked"':'').">".__("Log in afterwards")."</label>
			</td>
		</tr>
		<tr>
			<td colspan=\"2\" class=\"cell0 smallFonts\" style=\"padding:0.7em;\">";

if (Settings::get('email'))
	print "Specifying an email address is a requirement. By default, your email is made private. You can change this setting later in the \"edit profile\" page if you desire to do so.";
else
	print "Specifying an email address isn't a requirement, but is recommended. By default, your email is made private. You can change this setting later in the \"edit profile\" page if you desire to do so.";

print "		</td>
		</tr>
		<tr>
			<td colspan=\"2\" class=\"cell1 smallFonts\" style=\"padding:0.7em;\">
				Do you already have an account? Log into it <a href=\"/login/\">here</a>.
			</td>
		</tr>
	</table>";


function MakeOptions($fieldName, $checkedIndex, $choicesList) {
	$checks[$checkedIndex] = " checked=\"checked\"";
	foreach($choicesList as $key=>$val)
		$result .= format("
					<label>
						<input type=\"radio\" name=\"{1}\" value=\"{0}\"{2}>
						{3}
					</label>", $key, $fieldName, $checks[$key], $val);
	return $result;
}