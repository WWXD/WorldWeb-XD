<?php
// Check if we're deleting the profile picture
if(isset($_POST['removepicture'])) {
	$email = FetchResult("SELECT `email` FROM {users} WHERE `id`={0}", $userid);
	$gravatar = SqlEscape('https://www.gravatar.com/avatar/' . md5(strtolower(trim($email))) . '?s=128');

	// Save the gravatar to DB
	$why = Query("UPDATE {users} SET `picture`={0} WHERE `id`={1}", $gravatar, $userid);
}
