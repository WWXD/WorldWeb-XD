<?php
if (!defined('BLARG')) die();

$ajaxPage = TRUE;

if(isset($_GET['gfx']))
{

	if(isset($_GET['id']))
		$id = (int)$_GET['id'];
	else
		$id = $loguserid;

	$forums = Query("select id, title from {forums} order by id");
	$names = array();
	$posts = array();
	while($forum = Fetch($forums))
	{
		$names[] = $forum['title'];
		$posts[] = FetchResult("select count(*) from {posts} left join {threads} on {posts}.thread = {threads}.id where {posts}.user = {0} and {threads}.forum = {1}", $id, $forum['id']);
		//print $forum['title']." &rarr; ".$posts."<br/>";
	}


	$imgx = 320;
	$imgy = 200;
	$cx = 160;
	$cy = 90;
	$sx = 300;
	$sy = 170;
	$sz = 16;

	$data_sum = array_sum($posts);
	$angle = array();
	for($i = 0; $i <= count($posts); $i++)
	{
		$angle[$i] = (($posts[$i] / $data_sum) * 360);
		$angle_sum[$i] = array_sum($angle);
	}

	$im = imagecreate($imgx,$imgy);
	$background = imagecolorallocate($im, 255, 255, 255);

	for($i = 0; $i <= count($posts); $i++)
	{
		$r = rand(100, 255);
		$g = rand(100, 255);
		$b = rand(100,255);
		$colors[$i] = imagecolorallocate($im, $r, $g, $b);
		$colord[$i] = imagecolorallocate($im, ($r/1.5), ($g/1.5), ($b/1.5));
	}

	for($z = 1; $z <= $sz; $z++)
	{
		imagefilledarc($im, $cx, ($cy + $sz) - $z, $sx, $sy, 0, $angle_sum[0], $colord[0], IMG_ARC_EDGED);
		for($i = 1; $i <= count($posts); $i++)
		{
			imagefilledarc($im, $cx, ($cy + $sz) - $z, $sx, $sy, $angle_sum[$i - 1], $angle_sum[$i], $colord[$i], IMG_ARC_NOFILL);
		}
	}

	imagefilledarc($im, $cx, $cy, $sx, $sy, 0,$angle_sum[0], $colors[0], IMG_ARC_PIE);
	for($i = 1; $i <= count($posts); $i++)
	{
		imagefilledarc($im, $cx, $cy, $sx, $sy, $angle_sum[$i  -1], $angle_sum[$i], $colors[$i], IMG_ARC_PIE);
	}

	header('Content-type: image/png');
	imagepng($im);
	imagedestroy($im);
}



?>
