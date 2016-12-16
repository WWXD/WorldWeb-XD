<?php
//  Blargboard XD - User account registration page
//  Access: Guests.
//  Extra security by Super-toad 65 
if (!defined('BLARG')) die();

$title = __("Register");

echo "<script src=\"".resourceLink('js/register.js')."\"></script>
<script src=\"".resourceLink('js/zxcvbn.js')."\"></script>";

MakeCrumbs(array('' => __('Register')));

$sexes = array(__("Male"), __("Female"), __("N/A"));

if($loguserid)
	Kill(__("Why in the world are you trying to rereg? Isn't one account enough for you?"));

if($_POST['register']) {
	if (IsProxy() || IsProxyFSpamList()) {
		$adminemail = Settings::get('ownerEmail');
		if ($adminemail) $halp = '<br><br>If you aren\'t using a proxy, contact the board owner at: '.$adminemail;
		else $halp = '';

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

		if (stripos($cemail, in_array($emaildomainsblock)) !== FALSE)
			$err = __('An unknown error occured, please try again.');
		else if (!$cname)
			$err = __('Enter a username and try again.');
		else if ($uname == $cname)
			$err = __("This user name is already taken. Please choose another.");
		else if ($ipKnown >= 1)
			$err = __("Another user is already using this IP address.");
		else if (!$_POST['readFaq'])
			$err = format(__("You really should {0}read the FAQ{1}&hellip;"), "<a href=\"".actionLink("faq")."\">", "</a>");
		else if ($_POST['likesCake'])
			$err = __("An unknown error occured, please try again.");
		else if (strlen($_POST['pass']) < 8)
			$err = __("Your password must be at least eight characters long.");
		else if ($_POST['pass'] !== $_POST['pass2'])
			$err = __("The passwords you entered don't match.");
		else if (!$cemail)
			$err = __("You need to specify an email.");
		else if ($_POST['botprot'])
			$err = __("An unknown error occured, please try again.");
		else if ($uemail == $cemail)
			$err = __("An unknown error occured, please try again.");
		else if (!filter_var($cemail, FILTER_VALIDATE_EMAIL))
			$err = __("An unknown error occured, please try again.");

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
		$sha = doHash($_POST['pass'].SALT.$newsalt);
		$uid = FetchResult("SELECT id+1 FROM {users} WHERE (SELECT COUNT(*) FROM {users} u2 WHERE u2.id={users}.id+1)=0 ORDER BY id ASC LIMIT 1");
		if($uid < 1) $uid = 1;

		$rUsers = Query("insert into {users} (id, name, password, pss, primarygroup, regdate, lastactivity, lastip, email, sex, theme) values ({0}, {1}, {2}, {3}, {4}, {5}, {5}, {6}, {7}, {8}, {9})", 
			$uid, $_POST['name'], $sha, $newsalt, Settings::get('defaultGroup'), time(), $_SERVER['REMOTE_ADDR'], $_POST['email'], (int)$_POST['sex'], Settings::get("defaultTheme"));

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

$fields = array(
	'username' => "<input type=\"text\" id=\"un\" name=\"name\" maxlength=20 size=24 autocorrect=off autocapitalize=words value=\"".htmlspecialchars($_POST['name'])."\" class=\"required\">",
	'password' => "<input type=\"password\" id=\"pw\" name=\"pass\" size=24 class=\"required\">",
	'password2' => "<input type=\"password\" id=\"pw2\" name=\"pass2\" size=24 class=\"required\">",
	'email' => "<input type=\"email\" id=\"email\" type=email name=\"email\" value=\"".htmlspecialchars($_POST['email'])."\" maxlength=\"60\" size=24 class=\"required\">",
	'botprot' => "<input type=\"text\" id=\"botprot\" name=\"botprot\" style=\"display: none;\">",
	'sex' => MakeOptions("sex",$_POST['sex'],$sexes),
	'readfaq' => "<label><input type=\"checkbox\" name=\"readFaq\">".format(__("I have read the {0}FAQ{1}"), "<a href=\"".actionLink("faq")."\">", "</a>")."</label>",
	'autologin' => "<label><input type=\"checkbox\" checked=\"checked\" name=\"autologin\"".($_POST['autologin']?' checked="checked"':'').">".__("Log in afterwards")."</label>",

	'btnRegister' => "<input type=\"submit\" name=\"register\" value=\"".__("Register")."\">",
);

echo "<form action=\"".htmlentities(actionLink("register"))."\" method=\"post\">";

RenderTemplate('form_register', array('fields' => $fields));

echo "<span style=\"display : none;\"><input type=\"checkbox\" name=\"likesCake\"> I am a robot</span></form>";


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

?>
