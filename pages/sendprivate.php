<?php
//  AcmlmBoard XD - Private message sending/previewing page
//  Access: user
if (!defined('BLARG')) die();

$title = __("Private messages");

MakeCrumbs(array(actionLink("private") => __("Private messages"), '' => __("Send PM")));

if(!$loguserid) //Not logged in?
	Kill(__("You must be logged in to send private messages."));
	
CheckPermission('user.sendpms');

$draftID = 0;
$replyTo = 0;
$convStart = 0;
$urlargs = array();

$pid = (int)$_GET['pid'];
if($pid)
{
	$urlargs[] = 'pid='.$pid;
	
	// this shouldn't select drafts someone else is preparing for us
	// those drafts will have recipients stored in draft_to, with userto set to 0
	$rPM = Query("select * from {pmsgs} left join {pmsgs_text} on pid = {pmsgs}.id where (userfrom={0} OR userto={0}) and {pmsgs}.id = {1}", $loguserid, $pid);
	if(NumRows($rPM))
	{
		$pm = Fetch($rPM);
		$rUser = Query("select name from {users} where id = {0}", $pm['userfrom']);
		if(NumRows($rUser))
			$user = Fetch($rUser);
		else
			Kill(__("Unknown user."));
			
		$prefill = $pm['text'];
		$trefill = $pm['title'];
		
		if (!$pm['drafting'])
		{
			$convStart = $pm['conv_start'] ?: $pm['id'];
			$replyTo = $pm['userfrom'];
			
			$prefill = "[reply=\"".$user['name']."\"]".$prefill."[/reply]";

			if(strpos($pm['title'], "Re: Re: Re: ") !== false)
				$trefill = str_replace("Re: Re: Re: ", "Re*4: ", $pm['title']);
			else if(preg_match("'Re\*([0-9]+): 'se", $pm['title'], $reeboks))
				$trefill = "Re*" . ((int)$reeboks[1] + 1) . ": " . substr($pm['title'], strpos($pm['title'], ": ") + 2);
			else
				$trefill = "Re: ".$pm['title'];
				
			if(!isset($_POST['to']))
			$_POST['to'] = $user['name'];
		}
		else
		{
			$draftID = $pid;
			$convStart = $pm['conv_start'];
			
			$_POST['to'] = $pm['draft_to'];
		}
	} 
	else
		Kill(__("Unknown PM."));
}

$uid = (int)$_GET['uid'];
if($uid && !$_POST['to'])
{
	$urlargs[] = 'uid='.$uid;
	
	$rUser = Query("select name from {users} where id = {0}", $uid);
	if(NumRows($rUser))
	{
		$user = Fetch($rUser);
		$_POST['to'] = $user['name'];
	} else
		Kill(__("Unknown user."));
}


if ($_POST['actiondelete'] && $draftID)
{
	Query("DELETE FROM {pmsgs} WHERE id={0} AND drafting=1", $draftID);
	Query("DELETE FROM {pmsgs_text} WHERE pid={0}", $draftID);
	
	die(header("Location: ".actionLink("private")));
}


LoadPostToolbar();


$recipIDs = array();
if($_POST['to'])
{
	$recipients = explode(";", $_POST['to']);
	foreach($recipients as $to)
	{
		$to = trim(htmlentities($to));
		if($to == "")
			continue;

		$rUser = Query("select id from {users} where name={0} or displayname={0}", $to);
		if(NumRows($rUser))
		{
			$user = Fetch($rUser);
			$id = $user['id'];
			
			if(!in_array($id, $recipIDs))
				$recipIDs[] = $id;
		}
		else
			$errors .= format(__("Unknown user \"{0}\""), $to)."<br />";
	}

	$maxRecips = 5;
	if(count($recipIDs) > $maxRecips)
		$errors .= __("Too many recipients.");
	if($errors != "")
	{
		Alert($errors);
		unset($_POST['actionsend']);
		unset($_POST['actionsave']);
	}
}
else
{
	if($_POST['actionsend'] || $_POST['actionsave'])
	{
		Alert("Enter a recipient and try again.", "Your PM has no recipient.");
		unset($_POST['actionsend']);
		unset($_POST['actionsave']);
	}
}

if($_POST['actionsend'] || $_POST['actionsave'])
{
	if($_POST['title'])
	{
		$_POST['title'] = $_POST['title'];

		if($_POST['text'])
		{
			$wantDraft = ($_POST['actionsave'] ? 1:0);

			$bucket = "checkPost"; include(BOARD_ROOT."lib/pluginloader.php");

			$post = $_POST['text'];
			$post = preg_replace("'/me '","[b]* ".htmlspecialchars($loguser['name'])."[/b] ", $post); //to prevent identity confusion

			if($wantDraft)
			{
				if ($draftID)
				{
					Query("UPDATE {pmsgs_text} SET title={0}, text={1} WHERE pid={2}",
						$_POST['title'], $post, $draftID);
					Query("UPDATE {pmsgs} SET conv_start={0}, draft_to={1} WHERE id={2} AND drafting=1",
						$convStart, $_POST['to'], $draftID);
				}
				else
				{
					Query("insert into {pmsgs_text} (title,text) values ({0}, {1})", 
						$_POST['title'], $post);
					$pid = InsertId();
					
					Query("insert into {pmsgs} (id, userto, userfrom, conv_start, date, ip, drafting, draft_to) values ({0}, {1}, {2}, {3}, {4}, {5}, {6}, {7})", 
						$pid, 0, $loguserid, $convStart, time(), $_SERVER['REMOTE_ADDR'], 1, $_POST['to']);
				}

				die(header("Location: ".actionLink("private", "", "show=2")));
			}
			else
			{
				if ($draftID) 
				{
					$pid = $draftID;
					Query("DELETE FROM {pmsgs} WHERE id={0} AND drafting=1", $pid);
				}
				else
				{
					Query("insert into {pmsgs_text} (title,text) values ({0}, {1})", 
						$_POST['title'], $post);
					$pid = InsertId();
				}
				
				foreach($recipIDs as $recipient)
				{
					$cs = ($recipient == $replyTo) ? $convStart : 0;
					
					$rPM = Query("insert into {pmsgs} (id, userto, userfrom, conv_start, date, ip, msgread, drafting) values ({0}, {1}, {2}, {3}, {4}, {5}, 0, {6})", 
						$pid, $recipient, $loguserid, $cs, time(), $_SERVER['REMOTE_ADDR'], 0);
						
					SendNotification('pm', $pid, $recipient);
				}

				die(header("Location: ".actionLink("private", "", "show=1")));
			}
		} 
		else
		{
			Alert(__("Enter a message and try again."), __("Your PM is empty."));
		}
	} 
	else
	{
		Alert(__("Enter a title and try again."), __("Your PM is untitled."));
	}
}

if($_POST['text']) $prefill = $_POST['text'];
if($_POST['title']) $trefill = $_POST['title'];

if($_POST['actionpreview'] || $draftID)
{
	if($prefill)
	{
		$previewPost['text'] = $prefill;
		$previewPost['num'] = 0;
		$previewPost['posts'] = $loguser['posts'];
		$previewPost['id'] = 0;
		$previewPost['options'] = 0;

		foreach($loguser as $key => $value)
			$previewPost['u_'.$key] = $value;

		MakePost($previewPost, POST_SAMPLE);
	}
}

$fields = array(
	'to' => "<input type=\"text\" name=\"to\" size=40 maxlength=\"128\" value=\"".htmlspecialchars($_POST['to'])."\">",
	'title' => "<input type=\"text\" name=\"title\" size=80 maxlength=\"60\" value=\"".htmlspecialchars($trefill)."\">",
	'text' => "<textarea id=\"text\" name=\"text\" rows=\"16\">\n".htmlspecialchars($prefill)."</textarea>",
	
	'btnSend' => "<input type=\"submit\" name=\"actionsend\" value=\"".__("Send")."\">",
	'btnPreview' => "<input type=\"submit\" name=\"actionpreview\" value=\"".__("Preview")."\">",
	'btnSaveDraft' => "<input type=\"submit\" name=\"actionsave\" value=\"".__("Save draft")."\">",
	'btnDeleteDraft' => "<input type=\"submit\" name=\"actiondelete\" value=\"".__("Delete draft")."\" onclick=\"if(!confirm('Really delete this draft?'))return false;\">",
);

if (!$draftID) unset($fields['btnDeleteDraft']);

echo "
	<form name=\"postform\" action=\"\" method=\"post\">";

RenderTemplate('form_sendprivate', array('fields' => $fields, 'draftMode' => $draftID?true:false, 'maxRecipients' => 5));

echo "
	</form>
	<script type=\"text/javascript\">
		document.postform.text.focus();
	</script>
";

?>