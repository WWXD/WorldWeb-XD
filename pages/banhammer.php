<?php
if (!defined('BLARG')) die();

CheckPermission('admin.banusers');

$id = (int)$_GET['id'];
$user = Fetch(Query("SELECT u.(_userfields) FROM {users} u WHERE u.id={0}", $id));
if (!$user)
	Kill('Invalid user ID.');

if ($usergroups[$user['u_primarygroup']]['rank'] >= $loguserGroup['rank'])
	Kill('You may not ban a user whose level is equal to or above yours.');

if ($_POST['ban'])
{
	if ($_POST['token'] !== $loguser['token']) Kill('No.');
	
	if ($_POST['permanent'] )
	{
		$time = 0;
		$expire = 0;
	}
	else 
	{
		$time = $_POST['time'] * $_POST['timemult'];
		$expire = time() + $time;
	}
	
	if ($expire) $bantitle = __('Banned until ').formatdate($expire);
	else $bantitle = __('Banned permanently');
	
	if (trim($_POST['reason']))
		$bantitle .= __(': ').$_POST['reason'];
	
	Query("update {users} set tempbanpl = {0}, tempbantime = {1}, primarygroup = {4}, title = {3} where id = {2}", 
		$user['u_primarygroup'], $expire, $id, $bantitle, Settings::get('bannedGroup'));
	
	Report($loguser['name'].' banned '.$user['u_name'].($expire ? ' for '.TimeUnits($time) : ' permanently').
		($_POST['reason'] ? ': '.$_POST['reason']:'.'), true);

	die(header('Location: '.actionLink('profile', $id, '', $user['name'])));
}
else if ($_POST['unban'])
{
	if ($_POST['token'] !== $loguser['token']) Kill('No.');
	if ($user['u_primarygroup'] != Settings::get('bannedGroup')) Kill(__('This user is not banned.'));
	
	Query("update {users} set primarygroup = tempbanpl, tempbantime = {0}, title = {1} where id = {2}", 
		0, '', $id);
	
	Report($loguser['name'].' unbanned '.$user['u_name'].'.', true);

	die(header('Location: '.actionLink('profile', $id, '', $user['name'])));
}


if (isset($_GET['unban']))
{
	$title = __('Unban user');
	
	MakeCrumbs(array(actionLink("profile", $id, '', $user['u_name']) => htmlspecialchars($user['u_displayname']?$user['u_displayname']:$user['u_name']), 
		actionLink('banhammer', $id, 'unban=1') => __('Unban user')));
		
	$userlink = userLink(getDataPrefix($user, 'u_'));
	$fields = array(
		'target' => $userlink,
		
		'btnUnbanUser' => '<input type="submit" name="unban" value="Unban user">',
	);
	$template = 'form_unbanuser';
}
else
{
	$title = __('Ban user');
	
	MakeCrumbs(array(actionLink("profile", $id, '', $user['u_name']) => htmlspecialchars($user['u_displayname']?$user['u_displayname']:$user['u_name']), 
		actionLink('banhammer', $id) => __('Ban user')));
		
	$duration = '
	<label><input type="radio" name="permanent" value="0"> For: </label>
		<input type="text" name="time" size="4" maxlength="2">
		<select name="timemult">
			<option value="3600">hours</option>
			<option value="86400">days</option>
			<option value="604800">weeks</option>
		</select>
		<br>
	<label><input type="radio" name="permanent" value="1" checked="checked"> Permanent</label>';

	$userlink = userLink(getDataPrefix($user, 'u_'));
	$fields = array(
		'target' => $userlink,
		'duration' => $duration,
		'reason' => '<input type="text" name="reason" size=80 maxlength=200>',
		
		'btnBanUser' => '<input type="submit" name="ban" value="Ban user">',
	);
	$template = 'form_banuser';
}

echo '
	<form action="" method="POST">';
	
RenderTemplate($template, array('fields' => $fields));

echo '
		<input type="hidden" name="token" value="'.$loguser['token'].'">
	</form>';

?>