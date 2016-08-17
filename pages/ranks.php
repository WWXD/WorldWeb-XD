<?php
if (!defined('BLARG')) die();

$title = __("Ranks");
MakeCrumbs(array(actionLink("ranks") => __("Ranks")));

loadRanksets();
if(count($ranksetData) == 0)
	Kill(__("No ranksets have been defined."));

if(!isset($_GET['id']))
{
	$rankset = $loguser['rankset'];
	if(!$rankset || !isset($ranksetData[$rankset]))
	{
		$rankset = array_keys($ranksetData);
		$rankset = $rankset[0];
	}
	
	die(header("Location: ".actionLink("ranks", $rankset)));
}

$rankset = $_GET['id'];
if(!isset($ranksetData[$rankset]))
	Kill(__("Rankset not found."));

$ranksets = array();
foreach($ranksetNames as $name => $title)
{
	if($name == $rankset)
		$ranksets[] = $title;
	else
		$ranksets[] = actionLinkTag($title, 'ranks', $name);
}


$users = array();
$rUsers = Query("select u.(_userfields), u.(posts,lastposttime) from {users} u order by id asc");
while($user = Fetch($rUsers))
	$users[$user['u_id']] = getDataPrefix($user, "u_");

$ranks = $ranksetData[$rankset];

$ranklist = array();
for($i = 0; $i < count($ranks); $i++)
{
	$rdata = array();
	
	$rank = $ranks[$i];
	$nextRank = $ranks[$i+1];
	if($nextRank['num'] == 0)
		$nextRank['num'] = $ranks[$i]['num'] + 1;
	$members = array(); $inactive = 0; $total = 0;
	foreach($users as $user)
	{
		if($user['posts'] >= $rank['num'] && $user['posts'] < $nextRank['num'])
		{
			$total++;
			if ($user['lastposttime'] > time() - 2592000)
				$members[] = UserLink($user);
			else
				$inactive++;
		}
	}
	if ($inactive)
		$members[] = $inactive.' inactive';
	
	$showRank = HasPermission('admin.viewallranks') || $loguser['posts'] >= $rank['num'] || count($members) > 0;
	if($showRank)
		$rdata['rank'] = getRankHtml($rankset, $rank);
	else
		$rdata['rank'] = '???';

	if(count($members) == 0)
		$members = '&nbsp;';
	else
		$members = join(', ', $members);
		
	$rdata['posts'] = $showRank ? $rank['num'] : '???';
		
	$rdata['numUsers'] = $total;
	$rdata['users'] = $members;

	$ranklist[] = $rdata;
}

RenderTemplate('ranks', array('ranksets' => $ranksets, 'ranks' => $ranklist));

?>
