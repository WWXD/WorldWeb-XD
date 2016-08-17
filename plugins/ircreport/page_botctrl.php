<?php

if ($loguserid != 1) { require('pages/404.php'); return; }

$key = hash('sha256', "{$loguserid},{$loguser['pss']},".SALT.",sdi65fdsg4fd65g4fdg65g");

if ($_POST['stuff'] && $_POST['key'] === $key)
	ircReport($_POST['stuff']);

echo '
<form action="" method="POST">
	<table class="outline margin width100">
		<tr class="header1"><th>Secret IRC bot control</th></tr>
		<tr class="cell1"><td><input type="text" name="stuff" maxlength="300" style="width:100%;"></td></tr>
		<tr class="cell0"><td><input type="submit" value="Send"></td></tr>
	</table>
	<input type="hidden" name="key" value="'.$key.'">
</form>';

?>