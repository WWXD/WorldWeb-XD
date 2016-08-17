<?php
if (!defined('BLARG')) die();

$title = 'Forum permissions editor';

if (isset($_GET['fid']))
{
	CheckPermission('admin.editforums');
	$applyto = 1;
	$id = (int)$_GET['fid'];
	
	$forum = Fetch(Query("SELECT title FROM {forums} WHERE id={0}", $id));
	if (!$forum) Kill(__('Invalid forum ID.'));
	
	MakeCrumbs(array(actionLink('admin') => __('Admin'), 
		'' => __('Edit permissions for forum: ').htmlspecialchars($forum['title'])));
}
else
	Kill(__('Invalid parameters.'));


if ($_POST['saveaction'])
{
	if ($_POST['token'] !== $loguser['token'])
		Kill(__('No.'));
	
	foreach ($_POST as $k=>$v)
	{
		if (substr($k,0,6) != 'fperm_') continue;
		if ($v == $_POST['orig_'.$k]) continue;
		
		$perm = PermData($k);
		if ($v == 0)
			Query("DELETE FROM {permissions} WHERE applyto=0 AND id={0} AND perm={1} AND arg={2}",
				$perm['arg'], $perm['perm'], $id);
		else
			Query("INSERT INTO {permissions} (applyto,id,perm,arg,value) VALUES (0,{0},{1},{2},{3})
				ON DUPLICATE KEY UPDATE value={3}",
				$perm['arg'], $perm['perm'], $id, $v);
	}
	
	die(header('Location: '.actionLink('editforumperms', '', 'fid='.$id)));
}

$fpermlist = array();

echo '
	<form action="" method="POST">
		<table class="outline margin">
			<tr class="header1"><th colspan="2">'.__('Forum permissions').'</th></tr>';

$fperms = Query("SELECT p.*, g.id gid, g.name gname, g.color_unspec gcolor FROM {usergroups} g LEFT JOIN {permissions} p ON p.id=g.id AND p.applyto=0 AND p.arg={2}
	WHERE ISNULL(p.perm) OR SUBSTR(p.perm,1,6)={0} OR SUBSTR(p.perm,1,4)={1} ORDER BY g.type, g.rank", 
	'forum.', 'mod.', $id);
while ($fperm = Fetch($fperms))
{
	$fpermlist[$fperm['gid']][$fperm['perm']] = $fperm['value'];
	if (!$fpermlist[$fperm['gid']]['_gname'])
		$fpermlist[$fperm['gid']]['_gname'] = $fperm['gname'];
	if (!$fpermlist[$fperm['gid']]['_gcolor'])
		$fpermlist[$fperm['gid']]['_gcolor'] = $fperm['gcolor'];
}

if (!empty($fpermlist))
{
	foreach ($fpermlist as $gid=>$fpl)
		ForumPermTable($gid, $fpl);
}
else
	echo '
			<tr class="cell1"><td>'.__('No permissions.').'</td></tr>';

echo '
		</table>
		<table class="outline margin">
			<tr>
				<td class="cell1 center"><input type="submit" name="saveaction" value="'.__('Save all changes').'"></td>
			</tr>
		</table>
		<input type="hidden" name="token" value="'.htmlspecialchars($loguser['token']).'">
	</form>';
	

function PermSwitch($field, $val)
{
	return '
					<label class="highlight_red"><input type="radio" name="'.$field.'" value="-1"'.(($val==-1) ? ' checked="checked"':'').'> '.__('Deny').'</label>
					<label class="highlight_yellow"><input type="radio" name="'.$field.'" value="0"'.(($val==0) ? ' checked="checked"':'').'> '.__('Neutral').'</label>
					<label class="highlight_green"><input type="radio" name="'.$field.'" value="1"'.(($val==1) ? ' checked="checked"':'').'> '.__('Allow').'</label>
					<input type="hidden" name="orig_'.$field.'" value="'.$val.'">';
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

function ForumPermTable($gid, $fpl=array())
{
	global $permCats, $permDescs, $id;
	
	echo '
		<tr class="header0">
			<th colspan="2"><span'.($fpl['_gcolor']?' style="color: '.$fpl['_gcolor'].';"':'').'>'.htmlspecialchars($fpl['_gname']).'</span></th>
		</tr>';
	unset($fpl['_gname']);
	unset($fpl['_gcolor']);	
	
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
			$pkey = 'fperm_'.$gid.'_'.str_replace('.', '_', $permid);
		
			echo '
			<tr>
				<td class="cell2 center" style="width: 250px;">'.htmlspecialchars($permname).'</td>
				<td class="cell1">'.(CanEditPerm($permid, $id) ? PermSwitch($pkey, $fpl[$permid]) : PermLabel($fpl[$permid])).'</td>
			</tr>';
		}
	}
}

function PermData($key)
{
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