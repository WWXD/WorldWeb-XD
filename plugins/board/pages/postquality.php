<?php
if (!defined('BLARG')) die();

$title = 'Post quality stats';
MakeCrumbs(array(actionLink('postquality') => 'Post quality stats'));

$stuff = Query("	SELECT
						u.(_userfields),
						u.posts totalposts,
						(SELECT COUNT(*) FROM {posts} p WHERE p.user=u.id AND p.deleted!=0 AND p.deletedby!=u.id) deletedposts
					FROM
						{users} u
					WHERE ".($_GET['showbanned']?'':'u.primarygroup!={0} AND ')."u.posts>0
					HAVING (deletedposts / totalposts)>0.015
					ORDER BY (deletedposts / totalposts) DESC",
					Settings::get('bannedGroup'));
					
echo '
	<table class="outline margin">
		<tr class="cell0">
			<td colspan="5" class="center">
				<br>
				This page shows, for each user, how many of their posts were deleted by the staff.<br>
				(users with less than 2% of deleted posts aren\'t shown)<br>
				<br>
				If you are in the green part, you are fine, but try to be a little careful.<br>
				If you are in the orange part, you should really improve the quality of your posts.<br>
				If you are in the red part, you are walking on thin ice, and should think twice before posting again.<br>
				<br>
			</td>
		</tr>
		<tr class="header1">
			<th>User</th>
			<th>Posts</th>
			<th>Deleted</th>
			<th>Ratio</th>
			<th style="width:120px;">&nbsp;</th>
		</tr>';
					
$c = 1;
while ($user = Fetch($stuff))
{
	$udata = getDataPrefix($user, 'u_');
	$ulink = userLink($udata);
	
	$total = $user['totalposts'];
	$del = $user['deletedposts'];
	$ratio = ($del * 100) / $total;
	//if ($ratio <= 1) break;
	
	if ($ratio > 25)
		$color = '#f00';
	else if ($ratio > 10)
		$color = '#f80';
	else
		$color = '#0f0';
	
	$lol1 = '<span style="color:'.$color.';">';
	$lol2 = '</span>';
	
	$bar = '<div style="width:100px; border:1px solid #888; float:right;"><div style="background:'.$color.'; width:'.$ratio.'%; height:10px; float:right;"></div></div>';
	
	echo '
		<tr class="cell'.$c.'">
			<td>'.$ulink.'</td>
			<td class="center">'.$lol1.$total.$lol2.'</td>
			<td class="center">'.$lol1.$del.$lol2.'</td>
			<td class="center">'.$lol1.ceil($ratio).'%'.$lol2.'</td>
			<td>'.$bar.'</td>
		</tr>';
	
	$c = ($c==1) ? 2:1;
}

echo '
	</table>';

?>