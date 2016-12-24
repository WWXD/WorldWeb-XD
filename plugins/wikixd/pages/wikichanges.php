<?php

require 'wikilib.php';

$title = 'Wiki &raquo; Recent changes';
MakeCrumbs(array(actionLink('wiki') => 'Wiki', actionLink('wikichanges') => 'Recent changes'), $links);

$mydatefmt = 'm-d-Y';
if ($loguserid) $mydatefmt = $loguser['dateformat'];

$time = (int)$_GET['time'];
if (!$time) $time = 86400;

$spans = array(86400=>'Today', 604800=>'This week', 2592000=>'This month');
$spanList = "";
foreach($spans as $span=>$text)
{
	if ($span == $time)
		$spanList .= '<li>'.$text.'</li>';
	else
		$spanList .= actionLinkTagItem($text, 'wikichanges', '', 'time='.$span);
}
echo '
	<div class="smallFonts margin">
		View changes for:
		<ul class="pipemenu">
			'.$spanList.'
		</ul>
	</div>
';

echo '
	<table class="outline margin">
		<tr class="header1">
			<th>Page</th>
			<th style="width:100px;">&nbsp;</th>
		</tr>';
		
$today = cdate($mydatefmt, time());
$yesterday = cdate($mydatefmt, time()-86400);
$lastts = 'lol';
$c = 1;

$mindate = time() - $time;
$changes = Query("	SELECT
						pt.*,
						u.(_userfields)
					FROM
						{wiki_pages_text} pt
						LEFT JOIN {users} u ON u.id=pt.user
					WHERE
						pt.date > {0}
					ORDER BY pt.date DESC",
					$mindate);
if (!NumRows($changes))
{
	echo '
		<tr class="cell1">
			<td colspan="2">No changes to display.</td>
		</tr>';
}
else while ($change = Fetch($changes))
{
	$date = $change['date'];
	$ts = cdate($mydatefmt, $date);
	if ($ts == $today) $ts = 'Today';
	else if ($ts == $yesterday) $ts = 'Yesterday';
	
	if ($ts != $lastts)
	{
		$lastts = $ts;
		echo '
		<tr class="header0">
			<th colspan="2">'.$ts.'</th>
		</tr>';
	}
	
	$user = getDataPrefix($change, 'u_');
	$userlink = userLink($user);
	$date = formatdate($date);
	
	$links = actionLinkTagItem('View page', 'wiki', $change['id'], 'rev='.$change['revision']);
	$changetext = 'Page '.actionLinkTag(htmlspecialchars(url2title($change['id'])), 'wiki', $change['id']);
	if ($change['revision'] > 1) 
	{
		$changetext .= ' edited by '.$userlink.' on '.$date.' (revision '.$change['revision'].')';
		$links .= actionLinkTagItem('Diff', 'wikidiff', $change['id'], 'rev='.$change['revision']);
	}
	else 
		$changetext .= ' created by '.$userlink.' on '.$date;
		
	echo '
		<tr class="cell'.$c.'">
			<td>'.$changetext.'</td>
			<td><ul class="pipemenu">'.$links.'</ul></td>
		</tr>';
	
	$c = ($c==1)?2:1;
}

echo '
	</table>';

?>