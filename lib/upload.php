<?php
if (!defined('BLARG')) die();

define('POST_ATTACHMENT_CAP', 10*1024*1024);

function UploadFile($file, $parenttype, $parentid, $cap, $description='', $temporary=false)
{
	global $loguser, $loguserid;
	$targetdir = DATA_DIR.'uploads';
	
	$filedata = $_FILES[$file];
	$filename = $filedata['name'];
		
	if($filedata['size'] == 0)
		return true;
	else if($filedata['size'] > $cap)
		return false;
	else
	{
		CleanupUploads();
		
		$randomid = Shake();
		$pname = $randomid.'_'.Shake();
		
		$temp = $filedata['tmp_name'];

		Query("
			INSERT INTO {uploadedfiles} (id, physicalname, filename, description, user, date, parenttype, parentid, downloads, deldate) 
			VALUES ({0}, {1}, {2}, {3}, {4}, {5}, {6}, {7}, 0, {8})",
			$randomid, $pname, $filename, $description, $loguserid, time(), $parenttype, $parentid, $temporary?time():0);

		$fullpath = $targetdir.'/'.$pname;
		copy($temp, $fullpath);
		file_put_contents($fullpath.'.hash', hash_hmac_file('sha256', $fullpath, SALT));
		
		Report("[b]".$loguser['name']."[/] uploaded file \"[b]".$filename."[/]\"", false);

		return $randomid;
	}
}

function DeleteUpload($path, $userid)
{
	if (!file_exists($path.'.hash')) return;
	$hash = file_get_contents($path.'.hash');
	if ($hash === hash_hmac_file('sha256', $path, $userid.SALT))
	{
		@unlink($path);
		@unlink($path.'.hash');
	}
}

function CleanupUploads()
{
	$targetdir = DATA_DIR.'uploads';
	
	$timebeforedel = time()-604800; // one week
	$todelete = Query("SELECT physicalname, user, filename FROM {uploadedfiles} WHERE deldate!=0 AND deldate<{0}", $timebeforedel);
	if (NumRows($todelete))
	{
		while ($entry = Fetch($todelete))
		{
			Report("[b]{$entry['filename']}[/] deleted by auto-cleanup", false);
			DeleteUpload($targetdir.'/'.$entry['physicalname'], $entry['user']);
		}
			
		Query("DELETE FROM {uploadedfiles} WHERE deldate!=0 AND deldate<{0}", $timebeforedel);
	}
}


function HandlePostAttachments($postid, $final)
{
	$targetdir = DATA_DIR.'uploads';
	
	if (!Settings::get('postAttach')) return array();
	
	$attachs = array();
	
	if (isset($_POST['files']) && !empty($_POST['files']))
	{
		foreach ($_POST['files'] as $fileid=>$blarg)
		{
			if (isset($_POST['deletefile']) && $_POST['deletefile'][$fileid])
			{
				$todelete = Query("SELECT physicalname, user FROM {uploadedfiles} WHERE id={0}", $fileid);
				DeleteUpload($targetdir.'/'.$entry['physicalname'], $entry['user']);
				Query("DELETE FROM {uploadedfiles} WHERE id={0}", $fileid);
			}
			else
			{
				if ($final) Query("UPDATE {uploadedfiles} SET parentid={0}, deldate=0 WHERE id={1}", $postid, $fileid);
				$attachs[$fileid] = FetchResult("SELECT filename FROM {uploadedfiles} WHERE id={0}", $fileid);
			}
		}
	}

	foreach ($_FILES as $file=>$data)
	{
		if (in_array($data['name'], $attachs)) continue;
		
		$res = UploadFile($file, 'post_attachment', $postid, POST_ATTACHMENT_CAP, '', !$final);
		if ($res === false) return $res;
		if ($res === true) continue;
		$attachs[$res] = $data['name'];
	}
	
	return $attachs;
}

function PostAttachForm($files)
{
	if (!Settings::get('postAttach')) return;
	
	$fdata = array();
	asort($files);
	foreach ($files as $_fileid=>$filename)
	{
		$fileid = htmlspecialchars($_fileid);
		$fdata[] = 
			htmlspecialchars($filename).' 
			<label><input type="checkbox" name="deletefile['.$fileid.']" value="1"> Delete</label>
			<input type="hidden" name="files['.$fileid.']" value="blarg">';
	}
	
	$fields = array(
		'newFile' => '<input type="file" name="newfile">',
		
		'btnSave' => '<input type="submit" name="saveuploads" value="'.__('Save').'">',
	);
	
	RenderTemplate('form_attachfiles', array('files' => $fdata, 'fields' => $fields, 'fileCap' => BytesToSize(POST_ATTACHMENT_CAP)));
}
