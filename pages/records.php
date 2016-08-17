<?php
//  AcmlmBoard XD - The Records
//  Access: all
if (!defined('BLARG')) die();


$title = __("Records");

$df = "l, F jS Y, G:i:s";

$maxUsersText = $misc['maxuserstext'];
if($maxUsersText[0] == ":")
{
	$users = explode(":", $maxUsersText);

	$maxUsersText = "";
	foreach($users as $user)
	{
		if(!$user) continue;
		if($maxUsersText)
			$maxUsersText .= ", ";
		$maxUsersText .= UserLinkById($user);
	}
}

// Awesome way of calculating the mean birth date.
// I'm not sure if there's any problems with overflows and all. 
// But it seems to work fine :3

$sumAge = FetchResult("SELECT SUM(birthday) FROM {users} WHERE birthday != 0");
$countAge = FetchResult("SELECT COUNT(*) FROM {users} WHERE birthday != 0");
$avgAge = (int)($sumAge / $countAge);
$avgAge = formatBirthday($avgAge);

write(
"
<table class=\"outline margin width75\">
	<tr class=\"header0\">
		<th colspan=\"2\">
			".__("Highest Numbers")."
		</th>
	</tr>
	<tr class=\"cell0\">
		<td>
			".__("Highest number of posts in 24 hours")."
		</td>
		<td>
			".__("<strong>{0}</strong>, on {1} GMT")."
		</td>
	</tr>
	<tr class=\"cell1\">
		<td>
			".__("Highest number of posts in one hour")."
		</td>
		<td>
			".__("<strong>{2}</strong>, on {3} GMT")."
		</td>
	</tr>
	<tr class=\"cell0\">
		<td>
			".__("Highest number of users in five minutes")."
		</td>
		<td>
			".__("<strong>{4}</strong>, on {5} GMT")."
		</td>
	</tr>
	<tr class=\"cell1\">
		<td></td>
		<td>
			{6}
		</td>
	</tr>
	<tr class=\"cell0\">
		<td>
			".__("Average age of members")."
		</td>
		<td>
			".$avgAge."
		</td>
	</tr>
</table>
",	$misc['maxpostsday'], gmdate($df, $misc['maxpostsdaydate']),
	$misc['maxpostshour'], gmdate($df, $misc['maxpostshourdate']),
	$misc['maxusers'], gmdate($df, $misc['maxusersdate']),
	$maxUsersText);

$rStats = Query("show table status");
while($stat = Fetch($rStats))
	$tables[$stat['Name']] = $stat;

$tablelist = "";
$rows = $avg = $datlen = $idx = $datfree = 0;
foreach($tables as $table)
{
	$cellClass = ($cellClass+1) % 2;
	$tablelist .= format(
"
	<tr class=\"cell{0}\">
		<td class=\"cell2\">{1}</td>
		<td>
			{2}
		</td>
		<td>
			{3}
		</td>
		<td>
			{4}
		</td>
		<td>
			{5}
		</td>
		<td>
			{6}
		</td>
		<td>
			{7}
		</td>
	</tr>
",	$cellClass, $table['Name'], $table['Rows'], sp($table['Avg_row_length']),
	sp($table['Data_length']), sp($table['Index_length']), sp($table['Data_free']),
	sp($table['Data_length'] + $table['Index_length']));
	$rows += $table['Rows'];
	$avg += $table['Avg_row_length'];
	$datlen += $table['Data_length'];
	$idx += $table['Index_length'];
	$datfree += $table['Data_free'];
}

write(
"
<table class=\"outline margin\">
	<tbody>
		<tr class=\"header0\">
			<th colspan=\"7\" style=\"cursor:pointer;\" onclick=\"$('#fulltables').toggle();\">
				".__("Table Status (click to toggle details)")."
			</th>
		</tr>
		<tr class=\"header1\">
			<th>
				".__("Name")."
			</th>
			<th>
				".__("Rows")."
			</th>
			<th>
				".__("Avg. data/row")."
			</th>
			<th>
				".__("Data size")."
			</th>
			<th>
				".__("Index size")."
			</th>
			<th>
				".__("Unused data")."
			</th>
			<th>
				".__("Total size")."
			</th>
		</tr>
	</tbody>
	<tbody id=\"fulltables\" style=\"display:none;\">
		{0}
		<tr class=\"header1\">
			<th colspan=\"7\" style=\"height: 8px;\"></th>
		</tr>
	</tbody>
	<tbody>
		<tr class=\"cell2\">
			<td style=\"font-weight: bold;\">
				".__("Total")."
			</td>
			<td>
				{1}
			</td>
			<td>
				{2}
			</td>
			<td>
				{3}
			</td>
			<td>
				{4}
			</td>
			<td>
				{5}
			</td>
			<td>
				{6}
			</td>
		</tr>
	</tbody>
</table>
", $tablelist, $rows, sp($avg), sp($datlen), sp($idx), sp($datfree), sp($datlen + $idx));


// daily stats code
$mydatefmt = 'm-d-Y';
if ($loguserid) $mydatefmt = $loguser['dateformat'];

$timewarp = time()-2592000;

$utotal = FetchResult("SELECT COUNT(*) FROM {users} WHERE regdate<{0}", $timewarp);
$ttotal = FetchResult("SELECT COUNT(*) num FROM {threads} t LEFT JOIN {posts} p ON p.thread=t.id AND p.id=(SELECT MIN(p2.id) FROM {posts} p2 WHERE p2.thread=t.id) WHERE p.date<{0}", $timewarp);
$ptotal = FetchResult("SELECT COUNT(*) FROM {posts} WHERE date<{0}", $timewarp);

$usersperday = Query("SELECT FLOOR(regdate / 86400) day, COUNT(*) num FROM {users} WHERE regdate>={0} GROUP BY day ORDER BY day", $timewarp);
$threadsperday = Query("SELECT FLOOR(p.date / 86400) day, COUNT(*) num FROM {threads} t LEFT JOIN {posts} p ON p.thread=t.id AND p.id=(SELECT MIN(p2.id) FROM {posts} p2 WHERE p2.thread=t.id) WHERE p.date>={0} GROUP BY day ORDER BY day", $timewarp);
$postsperday = Query("SELECT FLOOR(date / 86400) day, COUNT(*) num FROM {posts} WHERE date>={0} GROUP BY day ORDER BY day", $timewarp);

$stats = array();
while ($u = Fetch($usersperday)) $stats[$u['day']]['u'] = $u['num'];
while ($t = Fetch($threadsperday)) $stats[$t['day']]['t'] = $t['num'];
while ($p = Fetch($postsperday)) $stats[$p['day']]['p'] = $p['num'];

echo '
<table class="outline margin width100">
	<tr class="header1">
		<th colspan="7">'.__('This month\'s daily stats').'</th>
	</tr>
	<tr class="header0">
		<th>'.__('Date').'</th>
		<th>'.__('Total users').'</th>
		<th>'.__('Total threads').'</th>
		<th>'.__('Total posts').'</th>
		<th>'.__('New users').'</th>
		<th>'.__('New threads').'</th>
		<th>'.__('New posts').'</th>
	</tr>';

$tc = 1;
$end = floor(time() / 86400);
for ($d = floor($timewarp / 86400); $d <= $end; $d++)
{
	if (!isset($stats[$d])) continue;
	
	$date = gmdate($mydatefmt, $d*86400);
	
	$unew = (int)$stats[$d]['u'];
	$tnew = (int)$stats[$d]['t'];
	$pnew = (int)$stats[$d]['p'];
	$utotal += $unew;
	$ttotal += $tnew;
	$ptotal += $pnew;
	
	echo '
	<tr class="cell'.$tc.'">
		<td class="cell0">'.$date.'</td>
		<td>'.$utotal.'</td>
		<td>'.$ttotal.'</td>
		<td>'.$ptotal.'</td>
		<td>'.$unew.'</td>
		<td>'.$tnew.'</td>
		<td>'.$pnew.'</td>
	</tr>';
	
	$tc = ($tc==1) ? 2:1;
}

echo '
</table>';


function sp($sz)
{
	return number_format($sz,0,'.',',');
}
?>
