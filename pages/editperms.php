<?php
if (!defined('BLARG')) die();

$title = 'Permissions editor';

if (isset($_GET['uid']))
{
	CheckPermission('admin.editusers');
	$applyto = 1;
	$id = (int)$_GET['uid'];
	
	$user = Fetch(Query("SELECT name,displayname,primarygroup FROM {users} WHERE id={0}", $id));
	if (!$user) Kill(__('Invalid user ID.'));
	$targetrank = $usergroups[$user['primarygroup']]['rank'];
	
	if ($targetrank > $loguserGroup['rank'])
		Kill(__('You may not edit permissions for this user.'));
	
	MakeCrumbs(array(actionLink('admin') => __('Admin'), 
		'' => __('Edit permissions for user: ').htmlspecialchars($user['displayname']?$user['displayname']:$user['name'])));
}
else if (isset($_GET['gid']))
{
	CheckPermission('admin.editgroups');
	$applyto = 0;
	$id = (int)$_GET['gid'];
	
	if (!$usergroups[$id]) Kill(__('Invalid group ID.'));
	$targetrank = $usergroups[$id]['rank'];
	
	if ($targetrank >= $loguserGroup['rank'] && !$loguser['root'])
		Kill(__('You may not edit permissions for this group.'));
	
	MakeCrumbs(array(actionLink('admin') => __('Admin'), 
		'' => __('Edit permissions for group: ').htmlspecialchars($usergroups[$id]['name'])));
}
else
	Kill(__('Invalid parameters.'));


if ($_POST['saveaction'] || $_POST['addfpermaction'])
{
	if ($_POST['token'] !== $loguser['token'])
		Kill(__('No.'));

	if ($_POST['addfpermaction'])
	{
		$fid = (int)$_POST['newforumid'];
		
		foreach ($_POST as $k=>$v)
		{
			if (substr($k,0,8) != 'fperm_0_') continue;
			if ($v == 0) continue;
			
			$perm = PermData($k);
			if (!CanEditPerm($perm['perm'], $fid)) continue;
			Query("INSERT INTO {permissions} (applyto,id,perm,arg,value) VALUES ({0},{1},{2},{3},{4})
				ON DUPLICATE KEY UPDATE value={4}",
				$applyto, $id, $perm['perm'], $fid, $v);
		}
	}
	
	foreach ($_POST as $k=>$v)
	{
		if (substr($k,0,5) != 'perm_' && substr($k,0,6) != 'fperm_') continue;
		if (substr($k,0,8) == 'fperm_0_') continue;
		if ($v == $_POST['orig_'.$k]) continue;
		
		$perm = PermData($k);
		if (!CanEditPerm($perm['perm'], $perm['arg'])) continue;
		if ($v == 0)
			Query("DELETE FROM {permissions} WHERE applyto={0} AND id={1} AND perm={2} AND arg={3}",
				$applyto, $id, $perm['perm'], $perm['arg']);
		else
			Query("INSERT INTO {permissions} (applyto,id,perm,arg,value) VALUES ({0},{1},{2},{3},{4})
				ON DUPLICATE KEY UPDATE value={4}",
				$applyto, $id, $perm['perm'], $perm['arg'], $v);
	}
	
	die(header('Location: '.actionLink('editperms', '', ($applyto==0?'gid=':'uid=').$id)));
}

?>
	<style type="text/css">
		.permselect, .permselect > option {color: black!important;}
	</style>
	<script type="text/javascript">
		function dopermselects()
		{
			$('.permselect').change(function() { this.style.background = this.selectedOptions[0].style.background; }).change();
		}
		$(document).ready(dopermselects);
	</script>
<?php

$permlist = array();
$fpermlist = array();

echo '
	<form action="" method="POST">
		<table class="layout-table">
		<tr><td style="width:50%;vertical-align:top;padding-right:0.5em;">
		<table class="outline margin">
			<tr class="header1"><th colspan="2">'.__('General permissions').'</th></tr>';
		
$perms = Query("SELECT * FROM {permissions} WHERE applyto={0} AND id={1} AND perm!={2} AND arg=0 ORDER BY perm", $applyto, $id, 'forum.viewforum');
while ($perm = Fetch($perms))
	$permlist[$perm['perm']] = $perm['value'];

foreach ($permCats as $cat=>$blarg)
	PermTable($cat);
		
echo '
		</table>
		</td><td style="vertical-align:top;padding-left:0.5em;">
		<table class="outline margin">
			<tr class="header1"><th colspan="2">'.__('Per-forum permissions').'</th></tr>';

$fperms = Query("SELECT p.*, f.title ftitle FROM {permissions} p LEFT JOIN {forums} f ON f.id=p.arg
	WHERE p.applyto={0} AND p.id={1} AND (SUBSTR(p.perm,1,6)={2} OR SUBSTR(p.perm,1,4)={3}) AND p.arg!=0 ORDER BY p.arg, p.perm", 
	$applyto, $id, 'forum.', 'mod.');
while ($fperm = Fetch($fperms))
{
	$fpermlist[$fperm['arg']][$fperm['perm']] = $fperm['value'];
	if (!$fpermlist[$fperm['arg']]['_ftitle'])
		$fpermlist[$fperm['arg']]['_ftitle'] = $fperm['ftitle'];
}

if (!empty($fpermlist))
{
	foreach ($fpermlist as $fid=>$fpl)
		ForumPermTable($fid, $fpl);
}
else
	echo '
			<tr class="cell1"><td>'.__('No permissions.').'</td></tr>';

echo '
		</table>
		<table class="outline margin">';
ForumPermTable(0);
echo '
		</table>
		</td></tr></table>
		<table class="outline margin">
			<tr>
				<td class="cell1 center"><input type="submit" name="saveaction" value="'.__('Save all changes').'"></td>
			</tr>
		</table>
		<input type="hidden" name="token" value="'.htmlspecialchars($loguser['token']).'">
	</form>';
	

function PermSwitch($field, $threeway, $_val)
{
	$val = $_val;
	if (!$threeway && $val == 0) $val = -1;
					
	return '
		<select class="permselect" name="'.$field.'">
			<option value="-1" '.($val==-1 ? 'selected="selected"':'').' style="background:#f88;">'.__('Deny').'</option>
			'.($threeway ? '<option value="0" '.($val==0 ? 'selected="selected"':'').' style="background:#ff8;">'.__('Neutral').'</option>':'').'
			<option value="1" '.($val==1 ? 'selected="selected"':'').' style="background:#8f8;">'.__('Allow').'</option>
		</select>
		<input type="hidden" name="orig_'.$field.'" value="'.$_val.'">';
}

function PermLabel($val)
{
	switch ($val)
	{
		case -1: return '<span class="highlight_red">'.__('Deny').'</span>';
		case 0: return '<span class="highlight_yellow">'.__('Neutral').'</span>';
		case 1: return '<span class="highlight_green">'.__('Allow').'</span>';
	}
	return '';
}

function PermTable($cat)
{
	global $permlist, $permCats, $permDescs, $applyto, $usergroups, $id;
	
	echo '
			<tr class="header0">
				<th colspan="2">'.htmlspecialchars($permCats[$cat]).'</th>
			</tr>';
	
	foreach ($permDescs[$cat] as $permid=>$permname)
	{
		if ($permid == 'forum.viewforum') continue;
		
		$pkey = 'perm_'.str_replace('.', '_', $permid);
		$isforumperm = (substr($permid,0,6) == 'forum.' || substr($permid,0,4) == 'mod.');
		
		echo '
			<tr>
				<td class="cell2 center" style="width: 250px;">'.htmlspecialchars($permname).'</td>
				<td class="cell1">'.(CanEditPerm($permid) ? PermSwitch($pkey, $applyto==1 || $usergroups[$id]['type']==1 || $isforumperm, $permlist[$permid]) : PermLabel($permlist[$permid])).'</td>
			</tr>';
	}
}

function ForumPermTable($fid, $fpl=array())
{
	global $permCats, $permDescs;
	
	if (!$fid)
	{
		echo '
			<tr class="header0">
				<th colspan="2">'.__('Add permission set').'</th>
			</tr>
			<tr>
				<td class="cell2 center">Forum</td>
				<td class="cell1">'.makeForumList('newforumid', 0).'</td>
			</tr>
			<tr class="header0">
				<th colspan="2" style="height:6px;"></th>
			</tr>';
	}
	else
	{
		echo '
			<tr class="header0">
				<th colspan="2">'.htmlspecialchars($fpl['_ftitle']).'</th>
			</tr>';
		unset($fpl['_ftitle']);
	}
	
	$lastcat = -1;
	$pd = array('forum' => $permDescs['forum'], 'mod' => $permDescs['mod']);
	foreach ($pd as $cat=>$perms)
	{
		if ($lastcat != $cat)
		{
			if ($lastcat != -1)
				echo '
			<tr class="header0">
				<th colspan="2" style="height:6px;"></th>
			</tr>';
			$lastcat = $cat;
		}
		
		foreach ($perms as $permid=>$permname)
		{
			$pkey = 'fperm_'.$fid.'_'.str_replace('.', '_', $permid);
		
			echo '
			<tr>
				<td class="cell2 center" style="width: 250px;">'.htmlspecialchars($permname).'</td>
				<td class="cell1">'.(CanEditPerm($permid, $fid) ? PermSwitch($pkey, true, $fpl[$permid]) : PermLabel($fpl[$permid])).'</td>
			</tr>';
		}
	}
	
	if (!$fid)
	{
		echo '
			<tr class="header0">
				<th colspan="2" style="height:6px;"></th>
			</tr>
			<tr>
				<td class="cell2">&nbsp;</td>
				<td class="cell1"><input type="submit" name="addfpermaction" value="'.__('Add permissions').'"></td>
			</tr>';
	}
}

function PermData($key)
{
	if ($key[0] == 'p')
	{
		$pname = substr($key, 5);
		$pname = str_replace('_', '.', $pname);
		return array('perm' => $pname, 'arg' => 0);
	}
	
	$seppos = strpos($key, '_', 6);
	$arg = intval(substr($key, 6, $seppos));
	$pname = substr($key, $seppos + 1);
	$pname = str_replace('_', '.', $pname);
	return array('perm' => $pname, 'arg' => $arg);
}

function CanEditPerm($perm, $arg=0)
{
	global $loguser;
	if ($loguser['root']) return true;
	return HasPermission($perm, $arg);
}

?>