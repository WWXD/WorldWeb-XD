<?php
// Check if there's a mail on the register form
if ($email != '') {
	$gravatar = SqlEscape('https://www.gravatar.com/avatar/' . md5(strtolower(trim($_POST['email']))) . '?s=128');

	// Save the gravatar to DB
	Query("UPDATE {users} SET `picture`={0} WHERE `id`={1}", $gravatar, $uid);
}
