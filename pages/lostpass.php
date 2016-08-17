<?php
if (!defined('BLARG')) die();

if(Settings::get("mailResetSender") == "")
	Kill(__("No sender specified for reset emails."));

if(isset($_GET['key']) && isset($_GET['id']))
{
	$user = Query("select pss from {users} where id = {0}", (int)$_GET['id']);
	if(NumRows($user) == 0)
		Kill(__("Invalid key."));

	$user = Fetch($user);

	$sha = doHash($_GET['key'].SALT.$user['pss']);

	$user = Query("select id, name, password, pss from {users} where id = {0} and lostkey = {1} and lostkeytimer > {2}", (int)$_GET['id'], $sha, (time() - (60*60)));

	if(NumRows($user) == 0)
		Kill(__("Invalid key."));
	else
		$user = Fetch($user);

	$newsalt = Shake();
	$newPass = randomString(8);
	$sha = doHash($newPass.SALT.$newsalt);

	Query("update {users} set lostkey = '', password = {0}, pss = {2} where id = {1}", $sha, (int)$_GET['id'], $newsalt);
	Kill(format(__("Your password has been reset to <strong>{0}</strong>. You can use this password to log in to the board. We suggest you change it as soon as possible."), $newPass), __("Password reset"));

}
else if(isset($_POST['action']))
{
	if($_POST['mail'] != $_POST['mail2'])
		Kill(__("The e-mail addresses you entered don't match, try again."));

	$user = Query("select id, name, password, email, lostkeytimer, pss from {users} where name = {0} and email = {1}", $_POST['name'], $_POST['mail']);
	if(NumRows($user) != 0)
	{
		$user = Fetch($user);
		if($user['lostkeytimer'] > time() - (60*60)) //wait an hour between attempts
			Kill(__("To prevent abuse, this function can only be used once an hour."), __("Slow down!"));

		//Make a RANDOM reset key.
		$resetKey = Shake();

		$hashedResetKey = doHash($resetKey.SALT.$user['pss']);

		$from = Settings::get("mailResetSender");
		$to = $user['email'];
		$subject = format(__("Password reset for {0}"), $user['name']);
		$message = format(__("A password reset was requested for your user account on {0}."), Settings::get("boardname"))."\n".__("If you did not submit this request, this message can be ignored.")."\n\n".__("To reset your password, visit the following URL:")."\n\n".absoluteActionLink("lostpass", $user['id'], "key=$resetKey")."\n\n".__("This link can be used once.");

		$headers = "From: ".$from."\r\n"."Reply-To: ".$from."\r\n"."X-Mailer: PHP";

		mail($to, $subject, wordwrap($message, 70), $headers);

		Query("update {users} set lostkey = {0}, lostkeytimer = {1} where id = {2}", $hashedResetKey, time(), $user['id']);
		
		Kill(__("Check your email in a moment and follow the link found therein."), __("Reset email sent"));
	}
	
	Kill(__('Invalid user name or email address.'));
}
else
{
	$title = __('Request password reset');
	MakeCrumbs(array(actionLink('login') => __('Log in'), '' => __('Request password reset')));
	
	echo "
	<form action=\"".htmlentities(actionLink("lostpass"))."\" method=\"post\">";
	
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

function randomString($len, $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789")
{
   $s = "";
   for ($i = 0; $i < $len; $i++)
   {
       $p = rand(0, strlen($chars)-1);
       $s .= $chars[$p];
   }
   return $s;
}

?>
