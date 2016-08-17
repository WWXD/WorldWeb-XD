<?php
//  AcmlmBoard XD - Thread submission/preview page
//  Access: users
if (!defined('BLARG')) die();

require(BOARD_ROOT.'lib/upload.php');

$title = __("New thread");

if(!$loguserid) //Not logged in?
	Kill(__("You must be logged in to post."));

if(isset($_POST['id']))
	$_GET['id'] = $_POST['id'];

if(!isset($_GET['id']))
	Kill(__("Forum ID unspecified."));

$fid = (int)$_GET['id'];

if (!HasPermission('forum.viewforum', $fid))
	Kill(__('You may not access this forum.'));

if (!HasPermission('forum.postthreads', $fid))
	Kill($loguser['banned'] ? __('You may not post because you are banned.') : __('You may not post threads in this forum.'));

$rFora = Query("select * from {forums} where id={0}", $fid);
if(NumRows($rFora))
	$forum = Fetch($rFora);
else
	Kill(__("Unknown forum ID."));

if($forum['locked'])
	Kill(__("This forum is locked."));

if(!isset($_POST['poll']) || isset($_GET['poll']))
	$_POST['poll'] = $_GET['poll'];
	
$isHidden = !HasPermission('forum.viewforum', $fid, true);
$urlname = $isHidden ? '' : $forum['title'];


$OnlineUsersFid = $fid;

MakeCrumbs(forumCrumbs($forum) + array('' => __("New thread")));

$attachs = array();

if (isset($_POST['saveuploads']))
{
	$attachs = HandlePostAttachments(0, false);
}
else if(isset($_POST['actionpreview']))
{
	$attachs = HandlePostAttachments(0, false);
	
	if($_POST['poll'])
	{
		$options = array();
		
		$pdata = array();
		$pdata['question'] = htmlspecialchars($_POST['pollQuestion']);
		$pdata['options'] = array();
		
		$noColors = 0;
		$defaultColors = array(
			          "#0000B6","#00B600","#00B6B6","#B60000","#B600B6","#B66700","#B6B6B6",
			"#676767","#6767FF","#67FF67","#67FFFF","#FF6767","#FF67FF","#FFFF67","#FFFFFF",);
			
		$totalVotes = 0;
		foreach ($_POST['pollOption'] as $i=>$opt)
		{
			$opt = array('choice'=>$opt, 'color'=>$_POST['pollColor'][$i], 'votes' => rand(1,10));
			$totalVotes += $opt['votes'];
			$options[] = $opt;
		}
		
		$pops = 0;
		foreach($options as $option)
		{
			$odata = array();
			
			$odata['color'] = htmlspecialchars($option['color']);
			if($odata['color'] == '')
				$odata['color'] = $defaultColors[($pops + 9) % 15];

			$votes = $option['votes'];

			$label = htmlspecialchars($option['choice']);
			$odata['label'] = $label;
				
			$odata['votes'] = $option['votes'];
			if($totalVotes > 0)
			{
				$width = (100 * $odata['votes']) / $totalVotes;
				$odata['percent'] = sprintf('%.4g', $width);
			}
			else
				$odata['percent'] = 0;

			$pdata['options'][] = $odata;
			$pops++;
		}
		
		$pdata['multivote'] = $_POST['multivote']?1:0;
		$pdata['votes'] = $totalVotes;
		$pdata['voters'] = $totalVotes;

		RenderTemplate('poll', array('poll' => $pdata));
	}

	$previewPost['text'] = $_POST['text'];
	$previewPost['num'] = $loguser['posts']+1;
	$previewPost['posts'] = $loguser['posts']+1;
	$previewPost['id'] = 0;
	$previewPost['options'] = 0;
	if($_POST['nopl']) $previewPost['options'] |= 1;
	if($_POST['nosm']) $previewPost['options'] |= 2;
	$previewPost['mood'] = (int)$_POST['mood'];
	$previewPost['has_attachments'] = !empty($attachs);
	$previewPost['preview_attachs'] = $attachs;

	foreach($loguser as $key => $value)
		$previewPost['u_'.$key] = $value;
		
	$previewPost['u_posts']++;

	MakePost($previewPost, POST_SAMPLE);
}
else if(isset($_POST['actionpost']))
{
	$titletags = parseThreadTags($_POST['title']);
	$trimmedTitle = trim(str_replace('&nbsp;', ' ', $titletags[0]));

	//Now check if the thread is acceptable.
	$rejected = false;

	if(!trim($_POST['text']))
	{
		Alert(__("Enter a message and try again."), __("Your post is empty."));
		$rejected = true;
	}
	else if(!$trimmedTitle)
	{
		Alert(__("Enter a thread title and try again."), __("Your thread is unnamed."));
		$rejected = true;
	}
	else if($_POST['poll'])
	{
		$optionCount = 0;
		foreach ($_POST['pollOption'] as $po)
			if ($po)
				$optionCount++;

		if($optionCount < 2)
		{
			Alert(__("You need to enter at least two options to make a poll."), __("Invalid poll."));
			$rejected = true;
		}

		if(!$rejected && !$_POST["pollQuestion"])
		{
			Alert(__("You need to enter a poll question to make a poll."), __("Invalid poll."));
			$rejected = true;
		}
	}
	else
	{
		$lastPost = time() - $loguser['lastposttime'];
		if($lastPost < Settings::get("floodProtectionInterval"))
		{
			//Check for last thread the user posted.
			$lastThread = Fetch(Query("SELECT * FROM {threads} WHERE user={0} ORDER BY id DESC LIMIT 1", $loguserid));

			//If it looks similar to this one, assume the user has double-clicked the button.
			if($lastThread['forum'] == $fid && $lastThread['title'] == $_POST['title'])
				die(header("Location: ".actionLink("thread", $lastThread['id'])));

			$rejected = true;
			Alert(__("You're going too damn fast! Slow down a little."), __("Hold your horses."));
		}
	}

	if(!$rejected)
	{
		$bucket = "checkPost"; include(BOARD_ROOT."lib/pluginloader.php");
	}

	if(!$rejected)
	{
		$post = $_POST['text'];

		$options = 0;
		if($_POST['nopl']) $options |= 1;
		if($_POST['nosm']) $options |= 2;

		if($_POST['iconid'])
		{
			$_POST['iconid'] = (int)$_POST['iconid'];
			if($_POST['iconid'] < 255)
				$iconurl = "img/icons/icon".$_POST['iconid'].".png";
			else
				$iconurl = $_POST['iconurl'];
		}
		else $iconurl = '';

		$closed = 0;
		$sticky = 0;
		if (HasPermission('mod.closethreads', $forum['id']))
			$closed = ($_POST['lock'] == 'on') ? '1':'0';
		if (HasPermission('mod.stickthreads', $forum['id']))
			$sticky = ($_POST['stick'] == 'on') ? '1':'0';

		if($_POST['poll'])
		{
			$doubleVote = ($_POST['multivote']) ? 1 : 0;
			$rPoll = Query("insert into {poll} (question, doublevote) values ({0}, {1})", $_POST['pollQuestion'], $doubleVote);
			$pod = InsertId();
			foreach ($_POST['pollOption'] as $i=>$opt)
			{
				if($opt)
				{
					$pollColor = filterPollColors($_POST['pollColor'][$i]);
					$rPollOption = Query("insert into {poll_choices} (poll, choice, color) values ({0}, {1}, {2})", $pod, $opt, $pollColor);
				}
			}
		}
		else
			$pod = 0;

		$rThreads = Query("insert into {threads} (forum, user, title, icon, lastpostdate, lastposter, closed, sticky, poll)
										  values ({0},   {1},  {2},   {3},  {4},          {1},        {5},   {6},     {7})",
										    $fid, $loguserid, $_POST['title'], $iconurl, time(), $closed, $sticky, $pod);
		$tid = InsertId();

		$rUsers = Query("update {users} set posts={0}, lastposttime={1} where id={2} limit 1", $loguser['posts']+1, time(), $loguserid);

		$rPosts = Query("insert into {posts} (thread, user, date, ip, num, options, mood)
									  values ({0},{1},{2},{3},{4}, {5}, {6})", $tid, $loguserid, time(), $_SERVER['REMOTE_ADDR'], $loguser['posts']+1, $options, (int)$_POST['mood']);
		$pid = InsertId();

		$rPostsText = Query("insert into {posts_text} (pid,text) values ({0},{1})", $pid, $post);

		$rFora = Query("update {forums} set numthreads=numthreads+1, numposts=numposts+1, lastpostdate={0}, lastpostuser={1}, lastpostid={2} where id={3} limit 1", time(), $loguserid, $pid, $fid);

		Query("update {threads} set date={2}, firstpostid={0}, lastpostid = {0} where id = {1}", $pid, $tid, time());
		
		$attachs = HandlePostAttachments($pid, true);
		Query("UPDATE {posts} SET has_attachments={0} WHERE id={1}", (!empty($attachs))?1:0, $pid);

		Report("New ".($_POST['poll'] ? "poll" : "thread")." by [b]".$loguser['name']."[/]: [b]".$_POST['title']."[/] (".$forum['title'].") -> [g]#HERE#?tid=".$tid, $isHidden);

		//newthread bucket
		$postingAsUser = $loguser;
		$thread['title'] = $_POST['title'];
		$thread['id'] = $tid;
		$bucket = "newthread"; include(BOARD_ROOT."lib/pluginloader.php");

		die(header("Location: ".actionLink("thread", $tid)));
	}
	else
		$attachs = HandlePostAttachments(0, false);
}

// Let the user try again.
$prefill = htmlspecialchars($_POST['text']);
$trefill = htmlspecialchars($_POST['title']);

if(!isset($_POST['iconid']))
	$_POST['iconid'] = 0;

function getCheck($name)
{
	if(isset($_POST[$name]) && $_POST[$name])
		return "checked=\"checked\"";
	else return "";
}

$iconNoneChecked = ($_POST['iconid'] == 0) ? "checked=\"checked\"" : "";
$iconCustomChecked = ($_POST['iconid'] == 255) ? "checked=\"checked\"" : "";

$i = 1;
$icons = "";
while(is_file("img/icons/icon".$i.".png"))
{
	$checked = ($_POST['iconid'] == $i) ? "checked=\"checked\" " : "";
	$icons .= "	<label>
					<input type=\"radio\" $checked name=\"iconid\" value=\"$i\">
					<img src=\"".resourceLink("img/icons/icon$i.png")."\" alt=\"Icon $i\" onclick=\"javascript:void()\">
				</label>";
	$i++;
}

$iconSettings = "
	<label>
		<input type=\"radio\" $iconNoneChecked name=\"iconid\" value=\"0\">
		<span>".__("None")."</span>
	</label>
	$icons
	<br />
	<label>
		<input type=\"radio\" $iconCustomChecked name=\"iconid\" value=\"255\">
		<span>".__("Custom")."</span>
	</label>
	<input type=\"text\" id=\"iconurl\" name=\"iconurl\" size=60 maxlength=\"100\" value=\"".htmlspecialchars($_POST['iconurl'])."\">";


if($_POST['addpoll'])
	$_POST['poll'] = 1;
else if($_POST['deletepoll'])
	$_POST['poll'] = 0;
else if ($_POST['pollAdd'])
{
	$_POST['pollOption'][] = '';
	$_POST['pollColor'][] = '';
}
else if ($_POST['pollRemove'])
{
	$i = array_keys($_POST['pollRemove']);
	$i = $i[0];
	
	array_splice($_POST['pollOption'], $i, 1);
	array_splice($_POST['pollColor'], $i, 1);
}

echo '<style type="text/css">';
if ($_POST['poll'])
	echo '.pollModeOff { display: none; }';
else
	echo '.pollModeOn { display: none; }';
echo '</style>';
	
$pollSettings = '<div id="pollOptions">';
if (!isset($_POST['pollOption']))
{
	$pollSettings .= '<div class="polloption">
		<input type="text" name="pollOption[0]" size=48 maxlength=40>
		&nbsp;Color: <input type="text" name="pollColor[0]" size=10 maxlength=7 class="color {hash:true,required:false,pickerFaceColor:\'black\',pickerFace:3,pickerBorder:0,pickerInsetColor:\'black\',pickerPosition:\'left\',pickerMode:\'HVS\'}">
		&nbsp; <input type="submit" name="pollRemove[0]" value="&#xD7;" onclick="removeOption(this.parentNode);return false;">
	</div>';
	$pollSettings .= '<div class="polloption">
		<input type="text" name="pollOption[1]" size=48 maxlength=40>
		&nbsp;Color: <input type="text" name="pollColor[1]" size=10 maxlength=7 class="color {hash:true,required:false,pickerFaceColor:\'black\',pickerFace:3,pickerBorder:0,pickerInsetColor:\'black\',pickerPosition:\'left\',pickerMode:\'HVS\'}">
		&nbsp; <input type="submit" name="pollRemove[1]" value="&#xD7;" onclick="removeOption(this.parentNode);return false;">
	</div>';
}
else
{
	foreach ($_POST['pollOption'] as $i=>$opt)
	{
		$color = htmlspecialchars($_POST['pollColor'][$i]);
		$opttext = htmlspecialchars($opt);
		
		$pollSettings .= '<div class="polloption">
		<input type="text" name="pollOption['.$i.']" value="'.$opttext.'" size=48 maxlength=40>
		&nbsp;Color: <input type="text" name="pollColor['.$i.']" value="'.$color.'" size=10 maxlength=7 class="color {hash:true,required:false,pickerFaceColor:\'black\',pickerFace:3,pickerBorder:0,pickerInsetColor:\'black\',pickerPosition:\'left\',pickerMode:\'HVS\'}">
		&nbsp; <input type="submit" name="pollRemove['.$i.']" value="&#xD7;" onclick="removeOption(this.parentNode);return false;">
	</div>';
	}
}
$pollSettings .= '</div>';
$pollSettings .= '<input type="submit" name="pollAdd" value="'.__('Add option').'" onclick="addOption();return false;">';


$moodSelects = array();
if($_POST['mood'])
	$moodSelects[(int)$_POST['mood']] = "selected=\"selected\" ";
$moodOptions = "<option ".$moodSelects[0]."value=\"0\">".__("[Default avatar]")."</option>\n";
$rMoods = Query("select mid, name from {moodavatars} where uid={0} order by mid asc", $loguserid);
while($mood = Fetch($rMoods))
	$moodOptions .= format(
"
	<option {0} value=\"{1}\">{2}</option>
",	$moodSelects[$mood['mid']], $mood['mid'], htmlspecialchars($mood['name']));

$mod_lock = '';
$mod_stick = '';
if (HasPermission('mod.closethreads', $forum['id']))
	$mod_lock = "<label><input type=\"checkbox\" ".getCheck("lock")." name=\"lock\">&nbsp;".__("Close thread", 1)."</label>\n";
if (HasPermission('mod.stickthreads', $forum['id']))
	$mod_stick = "<label><input type=\"checkbox\" ".getCheck("stick")."  name=\"stick\">&nbsp;".__("Sticky", 1)."</label>\n";


$fields = array(
	'title' => "<input type=\"text\" name=\"title\" size=80 maxlength=\"60\" value=\"$trefill\">",
	'icon' => $iconSettings,
	'pollQuestion' => "<input type=\"text\" name=\"pollQuestion\" value=\"".htmlspecialchars($_POST['pollQuestion'])."\" size=80 maxlength=\"100\">",
	'pollOptions' => $pollSettings,
	'pollMultivote' => "<label><input type=\"checkbox\" ".($_POST['multivote'] ? "checked=\"checked\"" : "")." name=\"multivote\">&nbsp;".__("Multivote", 1)."</label>",
	'text' => "<textarea id=\"text\" name=\"text\" rows=\"16\">\n$prefill</textarea>",
	'mood' => "<select size=1 name=\"mood\">".$moodOptions."</select>",
	'nopl' => "<label><input type=\"checkbox\" ".getCheck('nopl')." name=\"nopl\">&nbsp;".__("Disable post layout", 1)."</label>",
	'nosm' => "<label><input type=\"checkbox\" ".getCheck('nosm')." name=\"nosm\">&nbsp;".__("Disable smilies", 1)."</label>",
	'lock' => $mod_lock,
	'stick' => $mod_stick,
	
	'btnPost' => "<input type=\"submit\" name=\"actionpost\" value=\"".__("Post")."\">",
	'btnPreview' => "<input type=\"submit\" name=\"actionpreview\" value=\"".__("Preview")."\">",
	'btnAddPoll' => "<input type=\"submit\" name=\"addpoll\" value=\"".__("Add poll")."\" onclick=\"addPoll();return false;\">",
	'btnRemovePoll' => "<input type=\"submit\" name=\"deletepoll\" value=\"".__("Remove poll")."\" onclick=\"removePoll();return false;\">",
);


echo "
	<script src=\"".resourceLink("js/threadtagging.js")."\"></script>
	<script src=\"".resourceLink('js/polleditor.js')."\"></script>
	<form name=\"postform\" action=\"".htmlentities(actionLink("newthread", $fid))."\" method=\"post\" enctype=\"multipart/form-data\">";
					
RenderTemplate('form_newthread', array('fields' => $fields, 'pollMode' => (int)$_POST['poll']));

PostAttachForm($attachs);

echo "
		<input type=\"hidden\" name=\"poll\" id=\"pollModeVal\" value=\"".((int)$_POST['poll'])."\">
	</form>
	<script type=\"text/javascript\">
		document.postform.text.focus();
	</script>
";

LoadPostToolbar();

?>
