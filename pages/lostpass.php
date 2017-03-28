<?php
if (!defined('BLARG')) die();

if(Settings::get("mailResetSender") == "")
	Kill(__("No sender specified for reset emails."));

if($http->get('id'))) {
	$user = Query("select pss from {users} where id = {0}", (int)$http->get('id'));
	if(NumRows($user) == 0)
		Kill(__("Invalid key."));

	$user = Fetch($user);

	$sha = password_hash($http->get('key'), PASSWORD_DEFAULT);

	$user = Query("select id, name, password, pss from {users} where id = {0} and lostkey = {1} and lostkeytimer > {2}", (int)$http->get('id'), $sha, (time() - (60*60)));

	if(NumRows($user) == 0)
		Kill(__("Invalid key."));
	else
		$user = Fetch($user);

	$newsalt = Shake();
	$newPass = randomString(8);
	$sha = password_hash($newPass, PASSWORD_DEFAULT);

	Query("update {users} set lostkey = '', password = {0}, pss = {2} where id = {1}", $sha, (int)$http->get('id'), $newsalt);
	Kill(format(__("Your password has been reset to <strong>{0}</strong>. You can use this password to log in to the board. We suggest you change it as soon as possible."), $newPass), __("Password reset"));

} else if($http->post('action')) {
	if($http->post('mail') != $http->post('mail2'))
		Alert(__("The e-mail addresses you entered don't match, try again."));

	$user = Query("select id, name, password, email, lostkeytimer, pss from {users} where name = {0} and email = {1}", $http->post('name'), $http->post('mail'));
	if(NumRows($user) != 0) {
		$user = Fetch($user);
		if($user['lostkeytimer'] > time() - (60*60)) //wait an hour between attempts
			Kill(__("To prevent abuse, this function can only be used once an hour."), __("Slow down!"));

		//Make a RANDOM reset key.
		$resetKey = Shake();

		$hashedResetKey = password_hash($resetKey, PASSWORD_DEFAULT);

		$from = Settings::get("mailResetSender");
		$to = $user['email'];
		$subject = format(__("Password reset for {0}"), $user['name']);
		$message = format(__("A password reset was requested for your user account on {0}."), Settings::get("boardname"))."\n".__("If you did not submit this request, this message can be ignored.")."\n\n".__("To reset your password, visit the following URL:")."\n\n".absoluteActionLink("lostpass", $user['id'], "key=$resetKey")."\n\n".__("This link can be used once.");

		$headers = "From: ".$from."\r\n"."Reply-To: ".$from."\r\n"."X-Mailer: PHP";

		mail($to, $subject, wordwrap($message, 70), $headers);

		Query("update {users} set lostkey = {0}, lostkeytimer = {1} where id = {2}", $hashedResetKey, time(), $user['id']);

		Kill(__("Check your email in a moment and follow the link."), __("Reset email sent"));
	}

	Alert(__('Invalid user name or email address.'));
} else {
	$title = __('Request password reset');
	MakeCrumbs(array(actionLink('login') => __('Log in'), '' => __('Request password reset')));

	echo "
	<form action=\"".htmlentities(pageLink("lostpass"))."\" method=\"post\">";

	$fields = array(
		'username' => "<input type=\"text\" name=\"name\" maxlength=20 size=24>",
		'email' => "<input type=\"text\" name=\"mail\" maxlength=60 size=24>",
		'email2' => "<input type=\"text\" name=\"mail2\" maxlength=60 size=24>",

		'btnSendReset' => "<input type=\"submit\" name=\"action\" value=\"".__("Send reset email")."\">",
	);

	RenderTemplate('form_lostpass', array('fields' => $fields));

	echo "
	</form>
";

}

function randomString($len, $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789") {
	$s = "";
	for ($i = 0; $i < $len; $i++) {
		$p = rand(0, strlen($chars)-1);
		$s .= $chars[$p];
	}
	return $s;
}
