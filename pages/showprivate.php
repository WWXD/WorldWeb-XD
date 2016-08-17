<?php
//  AcmlmBoard XD - Private message display page
//  Access: user, specifically the sender or receiver.
if (!defined('BLARG')) die();

$title = __("Private messages");

if(!$loguserid)
	Kill(__("You must be logged in to view your private messages."));
	
$id = (int)$_REQUEST['id'];
if (!$id) Kill(__("No PM specified."));
$pmid = $id;

$staffpms = '';
if (HasPermission('admin.viewstaffpms')) $staffpms = ' OR userto={2}';

$snoop = isset($_GET['snooping']) && HasPermission('admin.viewpms');

if($snoop)
{
	$rPM = Query("select * from {pmsgs} left join {pmsgs_text} on pid = {pmsgs}.id where {pmsgs}.id = {0}", $id);
	Query("INSERT INTO {spieslog} (userid,date,pmid) VALUES ({0},UNIX_TIMESTAMP(),{1})", $loguserid, $id);
	
	Alert(__("You are snooping."));
}
else
	$rPM = Query("select * from {pmsgs} left join {pmsgs_text} on pid = {pmsgs}.id where (userto = {1} or userfrom = {1}{$staffpms}) and {pmsgs}.id = {0}", $id, $loguserid, -1);

if(NumRows($rPM))
	$pm = Fetch($rPM);
else
	Kill(__("Unknown PM"));

if($pm['drafting'] && !$snoop)
	Kill(__("Unknown PM")); //could say "PM is addresssed to you, but is being drafted", but what they hey?

$rUser = Query("select * from {users} where id = {0}", $pm['userfrom']);
if(NumRows($rUser))
	$user = Fetch($rUser);
else
	Kill(__("Unknown user."));
	
$links = array();

if(!$snoop && $pm['userto'] == $loguserid)
{
	Query("update {pmsgs} set msgread=1 where id={0}", $pm['id']);
	DismissNotification('pm', $pm['id'], $loguserid);
	
	$links[] = actionLinkTag(__("Send reply"), "sendprivate", "", "pid=".$pm['id']);
}
else if ($_GET['markread'])
{
	Query("update {pmsgs} set msgread=1 where id={0}", $pm['id']);
	DismissNotification('pm', $pm['id'], -1);
	
	die(header('Location: '.actionLink('private')));
}
	

$pmtitle = htmlspecialchars($pm['title']);
MakeCrumbs(array(actionLink("private") => __("Private messages"), '' => $pmtitle), $links);

$pm['num'] = 0;
$pm['posts'] = $user['posts'];
$pm['id'] = 0;

foreach($user as $key => $value)
	$pm['u_'.$key] = $value;

MakePost($pm, POST_PM);

?>
