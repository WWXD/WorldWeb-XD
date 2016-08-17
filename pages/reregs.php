<?php
if (!defined('BLARG')) die();

CheckPermission('admin.ipsearch');
	
$title = 'Rereg radar';
MakeCrumbs(array(actionLink("admin") => "Admin", actionLink('reregs') => 'Rereg radar'));

$ipm = Query("SELECT u.(_userfields), u.(lastactivity,lastip) FROM {users} u WHERE (SELECT COUNT(*) FROM {users} u2 WHERE u2.lastip=u.lastip)>1 ORDER BY lastactivity DESC");
$ipmatches = array();
while ($match = Fetch($ipm))
	$ipmatches[$match['u_lastip']][] = $match;
	
foreach ($ipmatches as $ip=>$match)
{
	$date = 0;
	foreach ($match as $user)
	{
		if ($user['u_lastactivity'] > $date)
			$date = $user['u_lastactivity'];
	}
	
	$ipmatches[$ip]['date'] = $date;
}

$passm = Query("SELECT u.(_userfields), m.(date,user,matches) FROM {passmatches} m LEFT JOIN {users} u ON u.id=m.user ORDER BY date DESC");
$passmatches = array();
while ($match = Fetch($passm))
	$passmatches[$match['m_user']] = $match;

?>
	<table class="outline margin">
		<tr class="header1">
			<th>Rereg radar</th>
		</tr>
		<tr class="cell2 center">
			<td>
				<br>
				This page lists users with matching IPs or passwords.<br>
				<br>
				The information isn't necessarily a proof that two users are the same person, 
				but can be used to confirm suspicions.<br>
				<br>
				Note: password matches are only checked when users register or log in.<br>
				<br>
			</td>
		</tr>
	</table>
	<table class="outline margin">
		<tr class="header1">
			<th colspan="3">IP matches</th>
		</tr>
		<tr class="header0">
			<th style="width:150px;">Date</th>
			<th style="width:150px;">IP</th>
			<th>Users</th>
		</tr>
<?php
	$c = 1;
	foreach ($ipmatches as $ip=>$match)
	{
		$date = formatdate($match['date']);
		unset($match['date']);
		
		$userlist = '';
		foreach ($match as $user)
		{
			$userdata = getDataPrefix($user, 'u_');
			$userlist .= userLink($userdata).', ';
		}
		$userlist = substr($userlist,0,-2);
		
		$fip = formatIP($ip);
		
		echo '
		<tr class="cell'.$c.'">
			<td class="center">'.$date.'</td>
			<td class="center">'.$fip.'</td>
			<td>'.$userlist.'</td>
		</tr>';
		$c = $c==1?2:1;
	}
?>
	</table>
	<table class="outline margin">
		<tr class="header1">
			<th colspan="2">Password matches</th>
		</tr>
		<tr class="header0">
			<th style="width:150px;">Date</th>
			<th>Users</th>
		</tr>
<?php
	$c = 1;
	$ulcache = array();
	foreach ($passmatches as $uid=>$match)
	{
		if (!$ulcache[$uid])
		{
			$userdata = Fetch(Query("SELECT u.(_userfields) FROM {users} u WHERE u.id={0}", $uid));
			$userdata = getDataPrefix($userdata, 'u_');
			$ulcache[$uid] = $userdata;
		}
		$ul1 = userLink($ulcache[$uid]);
		
		$date = formatdate($match['m_date']);
		
		$users = explode(',', $match['m_matches']);
		$userlist = '';
		foreach ($users as $u)
		{
			if (!$ulcache[$u])
			{
				$userdata = Fetch(Query("SELECT u.(_userfields) FROM {users} u WHERE u.id={0}", $u));
				$userdata = getDataPrefix($userdata, 'u_');
				$ulcache[$u] = $userdata;
			}
			$userlist .= userLink($ulcache[$u]).', ';
		}
		$userlist = substr($userlist,0,-2);
		
		echo '
		<tr class="cell'.$c.'">
			<td class="center">'.$date.'</td>
			<td>'.$ul1.', '.$userlist.'</td>
		</tr>';
		$c = $c==1?2:1;
	}
?>
	</table>