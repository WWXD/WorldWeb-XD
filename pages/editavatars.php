<?php
if (!defined('BLARG')) die();

$title = __("Mood avatars");

if(!$loguserid)
	Kill(__("You must be logged in to edit your avatars."));
	
CheckPermission('user.editprofile');
CheckPermission('user.editavatars');

MakeCrumbs(array(actionLink('profile', $loguserid, '', $loguser['name']) => htmlspecialchars($loguser['displayname']?$loguser['displayname']:$loguser['name']),
	actionLink("editavatars") => __("Mood avatars")));

if(isset($_POST['actionrename']) || isset($_POST['actiondelete']) || isset($_POST['actionadd']))
{
	$mid = (int)$_POST['mid'];
	if($_POST['actionrename'])
	{
		Query("update {moodavatars} set name={0} where mid={1} and uid={2}", $_POST['name'], $mid, $loguserid);
		
		die(header('Location: '.actionLink('editavatars')));
	}
	else if($_POST['actiondelete'])
	{
		Query("delete from {moodavatars} where uid={0} and mid={1}", $loguserid, $mid);
		Query("update {posts} set mood=0 where user={0} and mood={1}", $loguserid, $mid);
		if(file_exists(DATA_DIR."avatars/".$loguserid."_".$mid))
			unlink(DATA_DIR."avatars/".$loguserid."_".$mid);
			
		die(header('Location: '.actionLink('editavatars')));
	}
	else if($_POST['actionadd'])
	{
		$highest = FetchResult("select mid from {moodavatars} where uid={0} order by mid desc limit 1", $loguserid);
		if($highest < 1)
			$highest = 1;
		$mid = $highest + 1;

		//Begin copypasta from edituser/editprofile_avatar...
		if($fname = $_FILES['picture']['name'])
		{
			$fext = strtolower(substr($fname,-4));
			$error = "";

			$exts = array(".png",".jpg",".gif");
			$dimx = 200;
			$dimy = 200;
			$dimxs = 60;
			$dimys = 60;
			$size = 61440;

			$validext = false;
			$extlist = "";
			foreach($exts as $ext)
			{
				if($fext == $ext)
				$validext = true;
				$extlist .= ($extlist ? ", " : "").$ext;
			}
			if(!$validext)
				$error.="<li>".__("Invalid file type, must be one of:")." ".$extlist."</li>";

			if(!$error)
			{
				$tmpfile = $_FILES['picture']['tmp_name'];
				$file = DATA_DIR."avatars/".$loguserid."_".$mid;

				if($_POST['name'] == "")
					$_POST['name'] = "#".$mid;

				Query("insert into {moodavatars} (uid, mid, name) values ({0}, {1}, {2})", $loguserid, $mid, $_POST['name']);

				list($width, $height, $type) = getimagesize($tmpfile);

				if($type == 1) $img1 = imagecreatefromgif ($tmpfile);
				if($type == 2) $img1 = imagecreatefromjpeg($tmpfile);
				if($type == 3) $img1 = imagecreatefrompng ($tmpfile);

				if($width <= $dimx && $height <= $dimy && $type<=3)
					copy($tmpfile,$file);
				elseif($type <= 3)
				{
					$r = imagesx($img1) / imagesy($img1);
					if($r > 1)
					{
						$img2=imagecreatetruecolor($dimx,floor($dimy / $r));
						imagecopyresampled($img2,$img1,0,0,0,0,$dimx,$dimy/$r,imagesx($img1),imagesy($img1));
					} else
					{
						$img2=imagecreatetruecolor(floor($dimx * $r), $dimy);
						imagecopyresampled($img2,$img1,0,0,0,0,$dimx*$r,$dimy,imagesx($img1),imagesy($img1));
					}
					imagepng($img2,$file);
				} else
					$error.="<li>Invalid format.</li>";
			}
				
			if (!$error)
				die(header('Location: '.actionLink('editavatars')));
			else
				Kill(__("Could not update your avatar for the following reason(s):")."<ul>".$error."</ul>");
		}
	}
}

$moodRows = array();
$rMoods = Query("select mid, name from {moodavatars} where uid={0} order by mid asc", $loguserid);
while($mood = Fetch($rMoods))
{
	$row = array();
	
	$row['avatar'] = "<img src=\"".DATA_URL."avatars/{$loguserid}_{$mood['mid']}\" alt=\"\">";
	
	$row['field'] = "
				<form method=\"post\" action=\"".htmlentities(actionLink("editavatars"))."\">
					<input type=\"hidden\" name=\"mid\" value=\"{$mood['mid']}\">
					<input type=\"text\" id=\"name{$mood['mid']}\" name=\"name\" size=80 maxlength=60 value=\"".htmlspecialchars($mood['name'])."\"><br>
					<input type=\"submit\" name=\"actionrename\" value=\"".__("Rename")."\">
					<input type=\"submit\" name=\"actiondelete\" value=\"".__("Delete")."\" 
						onclick=\"if(!confirm('".__('Really delete this avatar? All posts using it will be changed to use your default avatar.')."'))return false;\">
				</form>";
			
	$moodRows[] = $row;
}

$newField = "
				<form method=\"post\" action=\"".htmlentities(actionLink("editavatars"))."\" enctype=\"multipart/form-data\">
					".__("Name:")." <input type=\"text\" id=\"newName\" name=\"name\" size=80 maxlength=60><br>
					".__("Image:")." <input type=\"file\" id=\"pic\" name=\"picture\"><br>
					<input type=\"submit\" name=\"actionadd\" value=\"".__("Add")."\">
				</form>";
				
RenderTemplate('moodavatars', array('avatars' => $moodRows, 'newField' => $newField));

?>