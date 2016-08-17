<?php

$title = 'Downloads';

$rootdir = DATA_DIR."uploader";

if ($_GET['id'])
{
	$dl = Fetch(Query("SELECT * FROM {uploader} WHERE id={0}", $_GET['id']));
	if (!$dl)
		Kill('Invalid download ID.');
	
	$canshow = FetchResult("SELECT showindownloads FROM {uploader_categories} WHERE id={0}", $dl['category']);
	if ($canshow != 1) 
		Kill('Invalid download ID.');
	
	MakeCrumbs(array(actionLink('downloads') => 'Downloads', actionLink('downloads', $dl['id']) => $dl['description']), '');
	
	echo '
		<table class="outline margin width100">
			<tr class="header1"><th colspan="2">'.htmlspecialchars($dl['description']).'</th></tr>';
			
	$filepath = $rootdir."/".$dl['physicalname'];
			
	$details = 'Uploaded on '.formatdate($dl['date']).' &mdash; Downloaded '.$dl['downloads'].' times<br>';
	$details .= 'File size: '.BytesToSize(@filesize($filepath)).' &mdash; MD5: '.@md5_file($filepath).' &mdash; SHA1: '.@sha1_file($filepath);
	
	$stuff = nl2br(htmlspecialchars($dl['big_description'])).'<br><br>'.$details;
	
	$outdated = '';
	$lastid = FetchResult("SELECT id FROM {uploader} WHERE category={0} ORDER BY date DESC LIMIT 1", $dl['category']);
	if ($lastid != $dl['id'])
	{
		$better = FetchResult("SELECT description FROM {uploader} WHERE id={0}", $lastid);
		$outdated = '
			<tr class="cell0">
				<td colspan="2" class="center">
					<span style="font-size:200%;"><strong>This download is outdated.</strong> We recommend that you check out '.actionLinkTag($better, 'downloads', $lastid).' instead.</span>
				</td>
			</tr>';
	}
	
	echo $outdated.'
			<tr class="cell1">
				<td style="padding: 0.3em;">
					'.$stuff.'
				</td>
				<td class="center">
					<a href="get.php?id='.$dl['id'].'&force">Download</a>
				</td>
			</tr>';
			
	echo '
		</table>';
	
	return;
}

MakeCrumbs(array(actionLink('downloads') => 'Downloads'), '');

$downloads = Query("SELECT u.*, uc.name catname FROM {uploader} u INNER JOIN {uploader_categories} uc ON uc.id=u.category WHERE uc.showindownloads=1 ORDER BY uc.ord, uc.id, u.date DESC");
$lastcat = -1;
while ($dl = Fetch($downloads))
{
	if ($lastcat != $dl['category'])
	{
		if ($lastcat != -1)
			echo '
		</table>';
		
		echo '
		<table class="outline margin width100">
			<tr class="header1"><th colspan="2">'.htmlspecialchars($dl['catname']).'</th></tr>';
		$c = 1;
		$lastcat = $dl['category'];
	}
			
	$filepath = $rootdir."/".$dl['physicalname'];
			
	$details = 'Uploaded on '.formatdate($dl['date']).' &mdash; Downloaded '.$dl['downloads'].' times<br>';
	$details .= 'File size: '.BytesToSize(@filesize($filepath)).' &mdash; MD5: '.@md5_file($filepath).' &mdash; SHA1: '.@sha1_file($filepath);
	
	$stuff = '<strong>'.htmlspecialchars($dl['description']).'</strong><br><br>'.nl2br(htmlspecialchars($dl['big_description'])).'<br><br>'.$details;
	
	echo '
			<tr class="cell'.$c.'">
				<td style="padding: 0.3em;">
					'.$stuff.'
				</td>
				<td class="center">
					<a href="get.php?id='.$dl['id'].'">Download</a>
				</td>
			</tr>';
	
	$c = $c?0:1;
}

echo '
		</table>';

?>