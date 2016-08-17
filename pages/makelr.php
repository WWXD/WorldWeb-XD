<?php
if (!defined('BLARG')) die();

if(!$loguser['root']) die('no');

$fora = Query("SELECT *, 0 AS l, 0 AS r, IF(catid>0,0,-catid) AS parent FROM {forums} ORDER BY catid,forder,id");
while ($f = Fetch($fora))
{
	$forums[$f['id']] = $f;
	$forums[$f['id']]['done'] = false;
}

maketree(0, 1);

function maketree($level, $curval)
{
	global $forums;
	
	foreach ($forums as $id=>$f)
	{
		if ($f['done']) continue;
		
		$parent = $f['parent'];
		if ($parent == $level)
		{
			$forums[$id]['l'] = $curval++;
			
			foreach ($forums as $cf)
			{
				if ($cf['parent'] == $id)
				{
					$curval = maketree($id, $curval);
					break;
				}
			}
			
			$forums[$id]['r'] = $curval++;
			$forums[$id]['done'] = true;
		}
	}
	
	return $curval;
}


?><table border=1><?php
foreach ($forums as $id=>$f)
{
	echo '<tr><td>FORUM #'.$id.':<td>'.$f['title'].'<td>parent='.$f['parent'].'<td>l='.$f['l'].'<td>r='.$f['r'];
	
	$old = Fetch(Query("SELECT l, r FROM {forums} WHERE id={0}", $id));
	echo '<td>oldl='.$old['l'].'<td>oldr='.$old['r'];
	
	Query("UPDATE {forums} SET l={0}, r={1} WHERE id={2}", $f['l'], $f['r'], $id);
	
	if ($old['l'] == $f['l'] && $old['r'] == $f['r'])
		echo '<td style="color:#0f0;">OK';
	else
		echo '<td style="color:#f00;">BAD/FIXED';
}

?></table>