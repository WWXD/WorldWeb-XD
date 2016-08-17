<?php
//  AcmlmBoard XD - Private message inbox/outbox viewer
//  Access: users
if (!defined('BLARG')) die();

$title = __("Private messages");

if(!$loguserid)
	Kill(__("You must be logged in to view your private messages."));

$user = $loguserid;
$userGet = '';
$snoop = '';
if(isset($_GET['user']) && HasPermission('admin.viewpms'))
{
	$user = (int)$_GET['user'];
	$snoop = "&snooping=1";
	$userGet = "&user=".$user;
}

if(isset($_POST['action']))
{
	if ($_POST['token'] !== $loguser['token']) Kill('No.');
	
	if($_POST['action'] == 'multidel' && $_POST['delete'] && !$snoop)
	{
		foreach($_POST['delete'] as $pid => $on)
		{
			$rPM = Query("select userto,userfrom,deleted,drafting from {pmsgs} where id = {0} and (userto = {1} or userfrom = {1})", $pid, $loguserid);
			if(NumRows($rPM))
			{
				$pm = Fetch($rPM);
				
				if ($pm['drafting'])
				{
					Query("DELETE FROM {pmsgs} WHERE id={0} AND drafting=1", $pid);
					Query("DELETE FROM {pmsgs_text} WHERE pid={0}", $pid);
				}
				else
				{
					if ($pm['userto'] == $loguserid) $pm['deleted'] |= 2;
					if ($pm['userfrom'] == $loguserid) $pm['deleted'] |= 1;
					
					/*if($pm['deleted'] == 3)
					{
						Query("delete from {pmsgs} where id = {0}", $pid);
						Query("delete from {pmsgs_text} where pid = {0}", $pid);
					}
					else*/
						Query("update {pmsgs} set deleted = {0} where id = {1} AND userto={2}", $pm['deleted'], $pid, $pm['userto']);
				}
			}
		}
		
		die(header('Location: '.$_SERVER['HTTP_REFERER']));
	}
}


$whereFrom = "p.userfrom = {0}";
$drafting = 0;
$deleted = 2;
$staffpms = '';

$showWhat = 0;

if(isset($_GET['show']))
{
	$showWhat = (int)$_GET['show'];
	
	$show = "&show=".$showWhat;
	if($showWhat == 1)
		$deleted = 1;
	else if($showWhat == 2)
		$drafting = 1;
	$onclause = 'p.userto';
}
else
{
	$whereFrom = "p.userto = {0}";
	if (HasPermission('admin.viewstaffpms') && $user==$loguserid) $staffpms = ' OR userto={4}';
	$onclause = 'p.userfrom';
}
$whereFrom .= " and p.drafting = ".$drafting;

$total = FetchResult("select count(*) from {pmsgs} p where ({$whereFrom}{$staffpms}) and !(p.deleted & {1})", $user, $deleted, null, null, -1);

$ppp = $loguser['threadsperpage'];

if(isset($_GET['from']))
	$from = (int)$_GET['from'];
else
	$from = 0;


$links = array();

$links[] = ($showWhat==0) ? __("Show received") : actionLinkTag(__("Show received"), "private", "", substr($userGet,1));
$links[] = ($showWhat==1) ? __("Show sent") : actionLinkTag(__("Show sent"), "private", "", "show=1".$userGet);
$links[] = ($showWhat==2) ? __("Show drafts") : actionLinkTag(__("Show drafts"), "private", "", "show=2".$userGet);
$links[] = actionLinkTag(__("Send new PM"), "sendprivate");

MakeCrumbs(array(actionLink("private") => __("Private messages")), $links);

$rPM = Query("
	SELECT 
		p.*,
		pt.*,
		u.(_userfields) 
	FROM 
		{pmsgs} p 
		LEFT JOIN {pmsgs_text} pt ON pt.pid = p.id 
		LEFT JOIN {users} u ON u.id={$onclause}
	WHERE 
	(".$whereFrom.$staffpms.") AND !(p.deleted & {1})
	ORDER BY p.date DESC LIMIT {2u}, {3u}", 
$user, $deleted, $from, $ppp, -1);

$pagelinks = PageLinks(actionLink("private", "", "$show$userGet&from="), $ppp, $from, $total);

RenderTemplate('pagelinks', array('pagelinks' => $pagelinks, 'position' => 'top'));

$pms = array();
while($pm = Fetch($rPM))
{
	$pmdata = array();
	
	if ($showWhat == 1 && $pm['userto'] == -1)
	{
		$pmdata['userlink'] = 'Staff';
	}
	else if ($pm['drafting'])
	{
		$pmdata['userlink'] = htmlspecialchars($pm['draft_to']);
	}
	else
	{
		$user = getDataPrefix($pm, 'u_');
		$pmdata['userlink'] = UserLink($user);
	}

	if(!$pm['msgread'])
		$pmdata['newIcon'] = "<div class=\"statusIcon new\"></div>";
	else
		$pmdata['newIcon'] = '';
		
	if ($pm['drafting'])
		$pmdata['link'] = actionLinkTag(htmlspecialchars($pm['title']), 'sendprivate', '', 'pid='.$pm['id'].$snoop);
	else
		$pmdata['link'] = actionLinkTag(htmlspecialchars($pm['title']), 'showprivate', $pm['id'], substr($snoop,1));

	$pmdata['deleteCheck'] = $snoop ? '' : "<input type=\"checkbox\" name=\"delete[{$pm['id']}]\">";
	
	$pmdata['formattedDate'] = formatdate($pm['date']);

	$pms[] = $pmdata;
}

echo "
	<form method=\"post\" action=\"\" id=\"pmform\">";
	
RenderTemplate('pmlist', array(
	'pms' => $pms,
	'inbox' => !$showWhat,
	'deleteCheckAll' => "<input type=\"checkbox\" id=\"ca\" onchange=\"checkAll();\">",
	'deleteCheckedLink' => "<a href=\"javascript:void();\" onclick=\"document.getElementById('pmform').submit();\">".__("Delete checked")."</a>",
));

echo "
		<input type=\"hidden\" name=\"action\" value=\"multidel\">
		<input type=\"hidden\" name=\"token\" value=\"{$loguser['token']}\">
	</form>
";

RenderTemplate('pagelinks', array('pagelinks' => $pagelinks, 'position' => 'bottom'));

?>
