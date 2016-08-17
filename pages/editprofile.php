<?php
if (!defined('BLARG')) die();

//Check Stuff
if(!$loguserid)
	Kill(__("You must be logged in to edit your profile."));

if (isset($_POST['action']) && $loguser['token'] != $_POST['key'])
	Kill(__("No."));

if(isset($_POST['editusermode']) && $_POST['editusermode'] != 0)
	$_GET['id'] = $_POST['userid'];

$editUserMode = false;

if (HasPermission('admin.editusers'))
{
	$userid = (isset($_GET['id'])) ? (int)$_GET['id'] : $loguserid;
	$editUserMode = true;
}
else
{
	CheckPermission('user.editprofile');
	$userid = $loguserid;
}

$user = Fetch(Query("select * from {users} where id={0}", $userid));
$usergroup = $usergroups[$user['primarygroup']];

$isroot = $usergroup['id'] == Settings::get('rootGroup');
$isbanned = $usergroup['id'] == Settings::get('bannedGroup');

if($editUserMode && $loguserid != $userid && $usergroup['rank'] > $loguserGroup['rank'])
	Kill(__("You may not edit a user whose rank is above yours."));

//Breadcrumbs
$uname = $user['name'];
if($user['displayname'])
	$uname = $user['displayname'];
	
$title = __('Edit profile');

makeCrumbs(array(actionLink("profile", $userid, "", $user['name']) => htmlspecialchars($uname), '' => __("Edit profile")));

loadRanksets();
$ranksets = $ranksetNames;
$ranksets = array_reverse($ranksets);
$ranksets[''] = __("None");
$ranksets = array_reverse($ranksets);

foreach($dateformats as $format)
	$datelist[$format] = ($format ? $format.' ('.cdate($format).')':'');
foreach($timeformats as $format)
	$timelist[$format] = ($format ? $format.' ('.cdate($format).')':'');

$sexes = array(__("Male"), __("Female"), __("N/A"));

$groups = array();
$r = Query("SELECT id,title FROM {usergroups} WHERE type=0 AND rank<={0} ORDER BY rank", $loguserGroup['rank']);
while ($g = Fetch($r))
	$groups[$g['id']] = htmlspecialchars($g['title']);
	

$pltype = Settings::get('postLayoutType');
	
$epPages = array();
$epCategories = array();
$epFields = array();


// EDITPROFILE TAB -- GENERAL -------------------------------------------------
AddPage('general', __('General'));

AddCategory('general', 'appearance', __('Appearance'));

if ($editUserMode || HasPermission('user.editdisplayname'))
	AddField('general', 'appearance', 'displayname', __('Display name'), 'text', array('width'=>24, 'length'=>20, 'hint'=>__('Leave this empty to use your login name.'), 'callback'=>'HandleDisplayname'));
	
AddField('general', 'appearance', 'rankset', __('Rankset'), 'select', array('options'=>$ranksets));

if ($editUserMode || (HasPermission('user.edittitle') && (HasPermission('user.havetitle') || $user['posts'] >= Settings::get('customTitleThreshold'))))
	AddField('general', 'appearance', 'title', __('Title'), 'text', array('width'=>80, 'length'=>255));


if ($editUserMode || HasPermission('user.editavatars'))
{
	AddCategory('general', 'avatar', __('Avatar'));

	AddField('general', 'avatar', 'picture', __('Avatar'), 'displaypic', array('hint'=>__('Maximum size is 200x200 pixels.')));
	AddField('general', 'avatar', 'minipic', __('Minipic'), 'minipic', array('hint'=>__('Maximum size is 16x16 pixels.')));
}


AddCategory('general', 'presentation', __('Presentation'));

AddField('general', 'presentation', 'threadsperpage', __('Threads per page'), 'number', array('min'=>1, 'max'=>99));
AddField('general', 'presentation', 'postsperpage', __('Posts per page'), 'number', array('min'=>1, 'max'=>99));
AddField('general', 'presentation', 'dateformat', __('Date format'), 'datetime', array('presets'=>$datelist, 'presetname'=>'presetdate'));
AddField('general', 'presentation', 'timeformat', __('Time format'), 'datetime', array('presets'=>$timelist, 'presetname'=>'presettime'));
AddField('general', 'presentation', 'fontsize', __('Font scale'), 'number', array('min'=>20, 'max'=>200));


AddCategory('general', 'options', __('Options'));

$blockall = $pltype ? __('Hide post layouts') : __('Hide signatures');
AddField('general', 'options', 'blocklayouts', $blockall, 'checkbox');


// EDITPROFILE TAB -- PERSONAL ------------------------------------------------
AddPage('personal', __('Personal'));

AddCategory('personal', 'personal', __('Personal information'));

AddField('personal', 'personal', 'sex', __('Gender'), 'radiogroup', array('options'=>$sexes));
AddField('personal', 'personal', 'realname', __('Real name'), 'text', array('width'=>24, 'length'=>60));
AddField('personal', 'personal', 'location', __('Location'), 'text', array('width'=>24, 'length'=>60));
AddField('personal', 'personal', 'birthday', __('Birthday'), 'birthday');

if ($editUserMode || HasPermission('user.editbio'))
	AddField('personal', 'personal', 'bio', __('Bio'), 'textarea');
	
AddField('personal', 'personal', 'timezone', __('Timezone offset'), 'timezone');


AddCategory('personal', 'contact', __('Contact information'));

AddField('personal', 'contact', 'homepageurl', __('Homepage URL'), 'text', array('width'=>60, 'length'=>60));
AddField('personal', 'contact', 'homepagename', __('Homepage name'), 'text', array('width'=>60, 'length'=>60));


// EDITPROFILE TAB -- ACCOUNT -------------------------------------------------
AddPage('account', __('Account settings'));

AddCategory('account', 'confirm', __('Password confirmation'));
AddField('account', 'confirm', 'info', '', 'label', array('value'=>__('Enter your password in order to edit account settings.')));
AddField('account', 'confirm', 'currpassword', __('Password'), 'passwordonce');


AddCategory('account', 'login', __('Login information'));

if ($editUserMode)
	AddField('account', 'login', 'name', __('User name'), 'text', array('width'=>24, 'length'=>20, 'callback' => 'HandleUsername'));
else
	AddField('account', 'login', 'name', __('User name'), 'label', array('value'=>htmlspecialchars($user['name'])));

AddField('account', 'login', 'password', __('Password'), 'password', array('callback'=>'HandlePassword'));


AddCategory('account', 'email', __('Email information'));

AddField('account', 'email', 'email', __('Email address'), 'email', array('width'=>24, 'length'=>60));
AddField('account', 'email', 'showemail', __('Make email address public'), 'checkbox');


if ($editUserMode)
{
	AddCategory('account', 'admin', __('Administrative stuff'));
	
	if ($isroot)
		AddField('account', 'admin', 'primarygroup', __('Primary group'), 'label', array('value'=>htmlspecialchars($usergroup['title'])));
	else
		AddField('account', 'admin', 'primarygroup', __('Primary group'), 'select', array('options'=>$groups));
	
	// TODO secondary groups!!
	
	if ($isbanned && $user['tempbantime'])
		AddField('account', 'admin', 'dopermaban', __('Make ban permanent'), 'checkbox', array('callback'=>'dummycallback'));
	
	AddField('account', 'admin', 'globalblock', __('Globally block layout'), 'checkbox');
	
	$aflags = array(0x1=>__('IP banned'), 0x2=>__('Errorbanned'));
	AddField('account', 'admin', 'flags', __('Misc. settings'), 'bitmask', array('options'=>$aflags));
}


// EDITPROFILE TAB -- LAYOUT --------------------------------------------------
if ($editUserMode || HasPermission('user.editpostlayout'))
{
	$pltext = $pltype ? __('Post layout') : __('Signature');
	AddPage('layout', $pltext);
	
	AddCategory('layout', 'postlayout', $pltext);
	
	if ($pltype) 
		AddField('layout', 'postlayout', 'postheader', __('Post header'), 'textarea', array('rows'=>16));
	AddField('layout', 'postlayout', 'signature', __('Signature'), 'textarea', array('rows'=>16));
	
	AddField('layout', 'postlayout', 'signsep', __('Show signature separator'), 'checkbox', array('negative'=>true));
	
	// TODO make a per-user permission for this one?
	if ($pltype == 2) 
		AddField('layout', 'postlayout', 'fulllayout', __('Apply layout to whole post box'), 'checkbox');
}


// EDITPROFILE TAB -- THEME ---------------------------------------------------
AddPage('theme', __('Theme'));

AddCategory('theme', 'theme', __('Theme'));
AddField('theme', 'theme', 'theme', '', 'themeselector');



//Allow plugins to add their own fields
$bucket = "editprofile"; include(BOARD_ROOT."lib/pluginloader.php");


$_POST['actionsave'] = (isset($_POST['actionsave']) ? $_POST['actionsave'] : '');

/* QUERY PART
 * ----------
 */

$failed = false;

if($_POST['actionsave'])
{
	// catch spamvertisers early
	if ((time() - $user['regdate']) < 300 && preg_match('@^\w+\d+$@', $user['name']))
	{
		$lolbio = strtolower($_POST['bio']);
		
		if ((substr($lolbio,0,7) == 'http://'
				|| substr($lolbio,0,12) == '[url]http://'
				|| substr($lolbio,0,12) == '[url=http://')
			&& ((substr($_POST['email'],0,strlen($user['name'])) == $user['name'])
				|| (substr($user['name'],0,6) == 'iphone')))
		{
			Query("UPDATE {users} SET primarygroup={0}, title={1} WHERE id={2}",
				Settings::get('bannedGroup'), 'Spamvertising', $loguserid);
			
			die(header('Location: '.actionLink('index')));
		}
	}
	
	
	$passwordEntered = false;

	if($_POST['currpassword'] != "")
	{
		$sha = doHash($_POST['currpassword'].SALT.$loguser['pss']);
		if($loguser['password'] == $sha)
			$passwordEntered = true;
		else
		{
			Alert(__("Invalid password"));
			$failed = true;
			$selectedTab = "account";

			$epFields['account.confirm']['currpassword']['fail'] = true;
		}
	}

	$query = "UPDATE {$dbpref}users SET ";
	$sets = array();
	$pluginSettings = unserialize($user['pluginsettings']);
			
	foreach ($epFields as $catid => $cfields)
	{
		foreach ($cfields as $field => $item)
		{
			if(substr($catid,0,8) == 'account.' && !$passwordEntered) 
				continue;
			
			if($item['callback'])
			{
				$ret = $item['callback']($field, $item);
				if($ret === true)
					continue;
				else if($ret != "")
				{
					Alert($ret, __('Error'));
					$failed = true;
					$selectedTab = $id;
					$item['fail'] = true;
				}
			}

			switch($item['type'])
			{
				case "label":
					break;
				case "text":
				case "textarea":
				case 'themeselector':
					$sets[] = $field." = '".SqlEscape($_POST[$field])."'";
					break;
				case "password":
					if($_POST[$field])
						$sets[] = $field." = '".SqlEscape($_POST[$field])."'";
					break;
				case "select":
					$val = $_POST[$field];
					if (array_key_exists($val, $item['options']))
						$sets[] = $field." = '".sqlEscape($val)."'";
					break;
				case "number":
					$num = (int)$_POST[$field];
					if($num < 1)
						$num = $item['min'];
					elseif($num > $item['max'])
						$num = $item['max'];
					$sets[] = $field." = ".$num;
					break;
				case "datetime":
					if($_POST[$item['presetname']] != -1)
						$_POST[$field] = $_POST[$item['presetname']];
					$sets[] = $field." = '".SqlEscape($_POST[$field])."'";
					break;
				case "checkbox":
					$val = (int)($_POST[$field] == "on");
					if($item['negative'])
						$val = (int)($_POST[$field] != "on");
					$sets[] = $field." = ".$val;
					break;
				case "radiogroup":
					if (array_key_exists($_POST[$field], $item['options']))
						$sets[] = $field." = '".SqlEscape($_POST[$field])."'";
					break;
				case "birthday":
					if($_POST[$field.'M'] && $_POST[$field.'D'] && $_POST[$field.'Y'])
					{
						$val = @mktime(0, 0, 0, (int)$_POST[$field.'M'], (int)$_POST[$field.'D'], (int)$_POST[$field.'Y']);
						if($val > time())
							$val = 0;
					}
					else
						$val = 0;
					$sets[] = $field." = '".$val."'";
					break;
				case "timezone":
					$val = ((int)$_POST[$field.'H'] * 3600) + ((int)$_POST[$field.'M'] * 60) * ((int)$_POST[$field.'H'] < 0 ? -1 : 1);
					$sets[] = $field." = ".$val;
					break;

				case "displaypic":
				case "minipic":
					if($_POST['remove'.$field])
					{
						$res = true;
						$sets[] = $field." = ''";
					}
					else
					{
						if($_FILES[$field]['name'] == "" || $_FILES[$field]['error'] == UPLOAD_ERR_NO_FILE)
							continue;
						$usepic = '';
						$res = HandlePicture($field, ($item['type']=='displaypic') ? 0:1, $usepic);
						if($res === true)
						{
							$sets[] = $field." = '".SqlEscape($usepic)."'";
						}
						else
						{
							Alert($res);
							$failed = true;
							$item['fail'] = true;
						}
					}
					
					// delete the old image if needed
					if ($res === true)
					{
						if (substr($user[$field],0,6) == '$root/')
						{
							// verify that the file they want us to delete is an internal avatar and not something else
							$path = str_replace('$root/', DATA_DIR, $user[$field]);
							if (!file_exists($path.'.internal')) continue;
							$hash = file_get_contents($path.'.internal');
							if ($hash === hash_hmac_file('sha256', $path, $userid.SALT))
							{
								@unlink($path);
								@unlink($path.'.internal');
							}
						}
					}
					break;
				
				case "bitmask":
					$val = 0;
					if ($_POST[$field])
					{
						foreach ($_POST[$field] as $bit)
							if ($bit && array_key_exists($bit, $item['options']))
								$val |= $bit;
					}
					$sets[] = $field." = ".(int)$val;
					break;
			}
			
			$epFields[$catid][$field] = $item;
		}
	}

	//Force theme names to be alphanumeric to avoid possible directory traversal exploits ~Dirbaio
	if(preg_match("/^[a-zA-Z0-9_]+$/", $_POST['theme']))
		$sets[] = "theme = '".SqlEscape($_POST['theme'])."'";

	$sets[] = "pluginsettings = '".SqlEscape(serialize($pluginSettings))."'";
	if ($editUserMode && ((int)$_POST['primarygroup'] != $user['primarygroup'] || $_POST['dopermaban'])) 
	{
		$sets[] = "tempbantime = 0";
		if ((int)$_POST['primarygroup'] != $user['primarygroup'])
			$sets[] = "tempbanpl = ".(int)$user['primarygroup'];
			
		Report($user['name']."'s primary group was changed from ".$groups[$user['primarygroup']]." to ".$groups[(int)$_POST['primarygroup']]);
	}

	$query .= join($sets, ", ")." WHERE id = ".$userid;
	if(!$failed)
	{
		RawQuery($query);

		$his = "[b]".$user['name']."[/]'s";
		if($loguserid == $userid)
			$his = HisHer($user['sex']);
		Report("[b]".$loguser['name']."[/] edited ".$his." profile. -> [g]#HERE#?uid=".$userid, 1);

		die(header("Location: ".actionLink("profile", $userid, '', $_POST['name']?:$user['name'])));
	}
}

//If failed, get values from $_POST
//Else, get them from $user

foreach ($epFields as $catid => $cfields)
{
	foreach ($cfields as $field => $item)
	{
		if ($item['type'] == "label" || $item['type'] == "password")
			continue;

		if(!$failed)
		{
			if(!isset($item['value']))
				$item['value'] = $user[$field];
		}
		else
		{
			if ($item['type'] == 'checkbox')
				$item['value'] = ($_POST[$field] == 'on') ^ $item['negative'];
			elseif ($item['type'] == 'timezone')
				$item['value'] = ((int)$_POST[$field.'H'] * 3600) + ((int)$_POST[$field.'M'] * 60) * ((int)$_POST[$field.'H'] < 0 ? -1 : 1);
			elseif ($item['type'] == 'birthday')
			{
				$item['value'] = @mktime(0, 0, 0, (int)$_POST[$field.'M'], (int)$_POST[$field.'D'], (int)$_POST[$field.'Y']);
			}
			else
				$item['value'] = $_POST[$field];
		}
		
		$epFields[$catid][$field] = $item;
	}
}


if($failed)
	$loguser['theme'] = $_POST['theme'];


function dummycallback($field, $item)
{
	return true;
}

function HandlePicture($field, $type, &$usepic)
{
	global $userid;
	
	if($type == 0)
	{
		$extensions = array(".png",".jpg",".jpeg",".gif");
		$maxDim = 200;
		$maxSize = 600 * 1024;
		$errorname = __('avatar');
	}
	else if($type == 1)
	{
		$extensions = array(".png", ".gif");
		$maxDim = 16;
		$maxSize = 100 * 1024;
		$errorname = __('minipic');
	}

	$fileName = $_FILES[$field]['name'];
	$fileSize = $_FILES[$field]['size'];
	$tempFile = $_FILES[$field]['tmp_name'];
	list($width, $height, $fileType) = getimagesize($tempFile);

	if ($type == 0 && ($width > 300 || $height > 300))
		return __("That avatar is definitely too big. The avatar field is meant for an avatar, not a wallpaper.");

	$extension = strtolower(strrchr($fileName, "."));
	if(!in_array($extension, $extensions))
		return format(__("Invalid extension used for {0}. Allowed: {1}"), $errorname, join($extensions, ", "));

	if($fileSize > $maxSize && !$allowOversize)
		return format(__("File size for {0} is too high. The limit is {1} bytes, the uploaded image is {2} bytes."), $errorname, $maxSize, $fileSize)."</li>";

	$ext = '.blarg';
	switch($fileType)
	{
		case 1:
			$sourceImage = imagecreatefromgif($tempFile);
			$ext = '.gif';
			break;
		case 2:
			$sourceImage = imagecreatefromjpeg($tempFile);
			$ext = '.jpg';
			break;
		case 3:
			$sourceImage = imagecreatefrompng($tempFile);
			$ext = '.png';
			break;
	}
	
	$randomcrap = '_'.time();
	$targetFile = false;

	$oversize = ($width > $maxDim || $height > $maxDim);
	if ($type == 0)
	{
		$targetFile = 'avatars/'.$userid.$randomcrap.$ext;

		if(!$oversize)
		{
			//Just copy it over.
			copy($tempFile, DATA_DIR.$targetFile);
		}
		else
		{
			//Resample that mother!
			$ratio = $width / $height;
			if($ratio > 1)
			{
				$targetImage = imagecreatetruecolor($maxDim, floor($maxDim / $ratio));
				imagecopyresampled($targetImage, $sourceImage, 0,0,0,0, $maxDim, $maxDim / $ratio, $width, $height);
			} else
			{
				$targetImage = imagecreatetruecolor(floor($maxDim * $ratio), $maxDim);
				imagecopyresampled($targetImage, $sourceImage, 0,0,0,0, $maxDim * $ratio, $maxDim, $width, $height);
			}
			imagepng($targetImage, DATA_DIR.$targetFile);
			imagedestroy($targetImage);
		}
	}
	elseif ($type == 1)
	{
		$targetFile = 'minipics/'.$userid.$randomcrap.$ext;

		if ($oversize)
		{
			//Don't allow minipics over $maxDim for anypony.
			return format(__("Dimensions of {0} must be at most {1} by {1} pixels."), $errorname, $maxDim);
		}
		else
			copy($tempFile, DATA_DIR.$targetFile);
	}
	
	// file created to verify that the avatar was created here
	file_put_contents(DATA_DIR.$targetFile.'.internal', hash_hmac_file('sha256', DATA_DIR.$targetFile, $userid.SALT));
	
	$usepic = '$root/'.$targetFile;
	return true;
}

// Special field-specific callbacks
function HandlePassword($field, $item)
{
	global $sets, $user, $loguser, $loguserid;
	if($_POST[$field] != "" && $_POST['repeat'.$field] != "" && $_POST['repeat'.$field] !== $_POST[$field])
	{
		return __("To change your password, you must type it twice without error.");
	}

	if($_POST[$field] != "" && $_POST['repeat'.$field] == "")
		$_POST[$field] = "";

	if($_POST[$field])
	{
		$newsalt = Shake();
		$sha = doHash($_POST[$field].SALT.$newsalt);
		$_POST[$field] = $sha;
		$sets[] = "pss = '".$newsalt."'";

		//Now logout all the sessions that aren't this one, for security.
		Query("DELETE FROM {sessions} WHERE id != {0} and user = {1}", doHash($_COOKIE['logsession'].SALT), $user['id']);
	}

	return false;
}

function HandleDisplayname($field, $item)
{
	global $user;
	if(IsReallyEmpty($_POST[$field]) || $_POST[$field] == $user['name'])
	{
		// unset the display name if it's really empty or the same as the login name.
		$_POST[$field] = "";
	}
	else
	{
		$dispCheck = FetchResult("select count(*) from {users} where id != {0} and (name = {1} or displayname = {1})", $user['id'], $_POST[$field]);
		if($dispCheck)
		{

			return format(__("The display name you entered, \"{0}\", is already taken."), SqlEscape($_POST[$field]));
		}
		else if($_POST[$field] !== ($_POST[$field] = preg_replace('/(?! )[\pC\pZ]/u', '', $_POST[$field])))
		{

			return __("The display name you entered cannot contain control characters.");
		}
	}
}

function HandleUsername($field, $item)
{
	global $user;
	if(IsReallyEmpty($_POST[$field]))
		$_POST[$field] = $user[$field];

	$dispCheck = FetchResult("select count(*) from {users} where id != {0} and (name = {1} or displayname = {1})", $user['id'], $_POST[$field]);
	if($dispCheck)
	{

		return format(__("The login name you entered, \"{0}\", is already taken."), SqlEscape($_POST[$field]));
	}
	else if($_POST[$field] !== ($_POST[$field] = preg_replace('/(?! )[\pC\pZ]/u', '', $_POST[$field])))
	{

		return __("The login name you entered cannot contain control characters.");
	}
}


/* EDITOR PART
 * -----------
 */

//Dirbaio: Rewrote this so that it scans the themes dir.
$dir = "themes/";
$themeList = "";
$themes = array();

// Open a known directory, and proceed to read its contents
if (is_dir($dir))
{
    if ($dh = opendir($dir))
    {
        while (($file = readdir($dh)) !== false)
        {
            if(filetype($dir . $file) != "dir") continue;
            if($file == ".." || $file == ".") continue;
            $infofile = $dir.$file."/themeinfo.txt";

            if(file_exists($infofile))
            {
		        $themeinfo = file_get_contents($infofile);
		        $themeinfo = explode("\n", $themeinfo, 2);

		        $themes[$file]['name'] = trim($themeinfo[0]);
		        $themes[$file]['author'] = trim($themeinfo[1]);
		    }
		    else
		    {
		        $themes[$file]['name'] = $file;
		        $themes[$file]['author'] = '';
		    }
			
			$themes[$file]['num'] = 0;
        }
        closedir($dh);
    }
}

$countdata = Query("SELECT theme, COUNT(id) num FROM {users} GROUP BY theme");
while ($c = Fetch($countdata))
	$themes[$c['theme']]['num'] = $c['num'];

asort($themes);

foreach($themes as $themeKey => $themeData)
{
	$themeName = $themeData['name'];
	$themeAuthor = $themeData['author'];
	$numUsers = $themeData['num'];

	$preview = "themes/".$themeKey."/preview.png";
	if(!is_file($preview))
		$preview = "img/nopreview.png";
	$preview = resourceLink($preview);

	$preview = "<img src=\"".$preview."\" alt=\"".$themeName."\" style=\"margin-bottom: 0.5em\">";

	if($themeAuthor)
		$byline = "<br>".nl2br($themeAuthor);
	else
		$byline = "";

	if($themeKey == $user['theme'])
		$selected = " checked=\"checked\"";
	else
		$selected = "";

	$themeList .= format(
"
	<div style=\"display: inline-block;\" class=\"theme\" title=\"{0}\">
		<input style=\"display: none;\" type=\"radio\" name=\"theme\" value=\"{3}\"{4} id=\"{3}\" onchange=\"ChangeTheme(this.value);\" />
		<label style=\"display: inline-block; clear: left; padding: 0.5em; {6} width: 260px; vertical-align: top\" onmousedown=\"void();\" for=\"{3}\">
			{2}<br />
			<strong>{0}</strong>
			{1}<br />
			{5}
		</label>
	</div>
",	$themeName, $byline, $preview, $themeKey, $selected, Plural($numUsers, "user"), "");
}

if(!isset($selectedTab))
{
	$selectedTab = "general";
	foreach($epPages as $id => $name)
	{
		if(isset($_GET[$id]))
		{
			$selectedTab = $id;
			break;
		}
	}
}


foreach ($epFields as $catid => $cfields)
{
	foreach ($cfields as $field => $item)
	{
		$output = '';
		
		if(isset($item['fail'])) 
			$item['caption'] = "<span style=\"color:#f44;\">{$item['caption']}</span>";

		switch($item['type'])
		{
			case "label":
				$output .= $item['value']."\n";
				break;
				
			case "password":
				$output = "<input type=\"password\" name=\"".$field."\" size=24> | ".__("Confirm:")." <input type=\"password\" name=\"repeat".$field."\" size=24>";
				break;
			case "passwordonce":
				$output = "<input type=\"password\" name=\"".$field."\" id=\"".$field."\" size=24>";
				break;
				
			case "color":
				$output = "<input type=\"text\" name=\"".$field."\" id=\"".$field."\" value=\"".htmlspecialchars($item['value'])."\" class=\"color{required:false}\">";
				break;
				
			case "birthday":
				if (!$item['value']) $bd = array('', '', '');
				else $bd = explode('-', date('m-d-Y', $item['value']));
				$output .= __('Month: ')."<input type=\"text\" name=\"{$field}M\" value=\"{$bd[0]}\" size=4 maxlength=2> ";
				$output .= __('Day: ')."<input type=\"text\" name=\"{$field}D\" value=\"{$bd[1]}\" size=4 maxlength=2> ";
				$output .= __('Year: ')."<input type=\"text\" name=\"{$field}Y\" value=\"{$bd[2]}\" size=4 maxlength=4> ";
				break;
				
			case "text":
			case "email":
				$output .= "<input id=\"".$field."\" name=\"".$field."\" type=\"".$item['type']."\" value=\"".htmlspecialchars($item['value'])."\"";
				if(isset($item['width']))
					$output .= " size=\"".$item['width']."\"";
				if(isset($item['length']))
					$output .= " maxlength=\"".$item['length']."\"";
				if(isset($item['more']))
					$output .= " ".$item['more'];
				$output .= ">\n";
				break;
				
			case "textarea":
				if(!isset($item['rows']))
					$item['rows'] = 8;
				$output .= "<textarea id=\"".$field."\" name=\"".$field."\" rows=\"".$item['rows']."\">\n".htmlspecialchars($item['value'])."</textarea>";
				break;
				
			case "checkbox":
				$output .= "<label><input id=\"".$field."\" name=\"".$field."\" type=\"checkbox\"";
				if((isset($item['negative']) && !$item['value']) || (!isset($item['negative']) && $item['value']))
					$output .= " checked=\"checked\"";
				$output .= "> ".$item['caption']."</label>\n";
				$item['caption'] = '';
				break;
				
			case "select":
				$disabled = isset($item['disabled']) ? $item['disabled'] : false;
				$disabled = $disabled ? "disabled=\"disabled\" " : "";
				$checks = array();
				$checks[$item['value']] = " selected=\"selected\"";
				$options = "";
				foreach($item['options'] as $key => $val)
					$options .= format("<option value=\"{0}\"{1}>{2}</option>", $key, $checks[$key], $val);
				$output .= format("<select id=\"{0}\" name=\"{0}\" size=\"1\" {2}>\n{1}\n</select>\n", $field, $options, $disabled);
				break;
				
			case "radiogroup":
				$checks = array();
				$checks[$item['value']] = " checked=\"checked\"";
				foreach($item['options'] as $key => $val)
					$output .= format("<label><input type=\"radio\" name=\"{1}\" value=\"{0}\"{2}>{3}</label>", $key, $field, $checks[$key], $val).' ';
				break;
				
			case "displaypic":
			case "minipic":
				$output .= "<input type=\"file\" id=\"".$field."\" name=\"".$field."\">\n";
				$output .= "<label><input type=\"checkbox\" name=\"remove".$field."\"> ".__("Remove")."</label>\n";
				break;
				
			case "number":
				//$output .= "<input type=\"number\" id=\"".$field."\" name=\"".$field."\" value=\"".$item['value']."\" />";
				$output .= "<input type=\"text\" id=\"".$field."\" name=\"".$field."\" value=\"".$item['value']."\" size=\"6\" maxlength=\"4\">";
				break;
				
			case "datetime":
				$output .= "<input type=\"text\" id=\"".$field."\" name=\"".$field."\" value=\"".$item['value']."\">\n";
				$output .= __("or preset:")."\n";
				$options = "<option value=\"-1\">".__("[select]")."</option>";
				foreach($item['presets'] as $key => $val)
					$options .= format("<option value=\"{0}\">{1}</option>", $key, $val);
				$output .= format("<select id=\"{0}\" name=\"{0}\" size=\"1\" >\n{1}\n</select>\n", $item['presetname'], $options);
				break;
				
			case "timezone":
				$output .= "<input type=\"text\" name=\"".$field."H\" size=\"2\" maxlength=\"3\" value=\"".(int)($item['value']/3600)."\">\n";
				$output .= ":\n";
				$output .= "<input type=\"text\" name=\"".$field."M\" size=\"2\" maxlength=\"3\" value=\"".floor(abs($item['value']/60)%60)."\">";
				break;
				
			case "bitmask":
				foreach($item['options'] as $key => $val)
					$output .= format("<label><input type=\"checkbox\" name=\"{1}[]\" value=\"{0}\"{2}> {3}</label> &nbsp;", 
						$key, $field, ($item['value'] & $key) ? ' checked="checked"' : '', $val);
				$item['caption'] = '';
				break;
				
			case 'themeselector':
				$output .= $themeList;
				break;
		}
		if(isset($item['extra']))
			$output .= " ".$item['extra'];
			
		$item['html'] = $output;
		$epFields[$catid][$field] = $item;
	}
}


echo "
	<form action=\"".htmlentities(actionLink("editprofile"))."\" method=\"post\" enctype=\"multipart/form-data\">
";

RenderTemplate('form_editprofile', array(
	'pages' => $epPages, 
	'categories' => $epCategories, 
	'fields' => $epFields,
	'selectedTab' => $selectedTab,
	'btnEditProfile' => "<input type=\"submit\" id=\"submit\" name=\"actionsave\" value=\"".__("Save")."\">"));

echo "
		<input type=\"hidden\" name=\"editusermode\" value=\"1\">
		<input type=\"hidden\" name=\"userid\" value=\"{$userid}\">
		<input type=\"hidden\" name=\"key\" value=\"{$loguser['token']}\">
	</form>
";


function IsReallyEmpty($subject)
{
	$trimmed = trim(preg_replace("/&.*;/", "", $subject));
	return strlen($trimmed) == 0;
}


function AddPage($page, $name)
{
	global $epPages, $epCategories;
	
	$epPages[$page] = $name;
	$epCategories[$page] = array();
}

function AddCategory($page, $cat, $name)
{
	global $epCategories, $epFields;
	
	$epCategories[$page][$page.'.'.$cat] = $name;
	$epFields[$page.'.'.$cat] = array();
}

function AddField($page, $cat, $id, $label, $type, $misc=null)
{
	global $epFields;
	
	$field = array(
		'caption' => $label,
		'type' => $type,
	);
	
	if ($misc)
		$field = array_merge($field, $misc);
	
	$epFields[$page.'.'.$cat][$id] = $field;
}

?>
<script type="text/javascript">
var homepagename = "<?php echo addslashes($epFields['personal.contact']['homepagename']['value']); ?>";
setTimeout(function()
{
	// kill Firefox's dumb autofill
	$('#homepagename').val(homepagename);
	$('#currpassword').keyup();
}, 200);

$('#currpassword').keyup(function()
{
	var fields = $('#account').find('input:not(#currpassword),select');
	if (this.value == '')
		fields.attr('disabled', 'disabled');
	else
		fields.removeAttr('disabled');
});
</script>
