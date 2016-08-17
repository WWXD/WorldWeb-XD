<?php
//  AcmlmBoard XD - Reply submission/preview page
//  Access: users
if (!defined('BLARG')) die();

require(BOARD_ROOT.'lib/upload.php');

$title = __("New reply");

if(!$loguserid) //Not logged in?
	Kill(__("You must be logged in to post."));

if(isset($_POST['id']))
	$_GET['id'] = $_POST['id'];

if(!isset($_GET['id']))
	Kill(__("Thread ID unspecified."));

$tid = (int)$_GET['id'];

$rThread = Query("select * from {threads} where id={0}", $tid);
if(NumRows($rThread))
{
	$thread = Fetch($rThread);
	$fid = $thread['forum'];
}
else
	Kill(__("Unknown thread ID."));
	
if (!HasPermission('forum.viewforum', $fid))
	Kill(__('You may not access this forum.'));

if (!HasPermission('forum.postreplies', $fid))
	Kill($loguser['banned'] ? __('You may not post because you are banned.') : __('You may not post in this forum.'));

$rFora = Query("select * from {forums} where id={0}", $fid);
if(NumRows($rFora))
	$forum = Fetch($rFora);
else
	Kill("Unknown forum ID.");
$fid = $forum['id'];

$isHidden = !HasPermission('forum.viewforum', $fid, true);

if($thread['closed'] && !HasPermission('mod.closethreads', $fid))
	Kill(__("This thread is locked."));

$OnlineUsersFid = $fid;

LoadPostToolbar();

$tags = ParseThreadTags($thread['title']);
$urlname = $isHidden ? '' : $tags[0];
MakeCrumbs(forumCrumbs($forum) + array(actionLink("thread", $tid, '', $urlname) => $tags[0], '' => __("New reply")));

if(!$thread['sticky'] && Settings::get("oldThreadThreshold") > 0 && $thread['lastpostdate'] < time() - (2592000 * Settings::get("oldThreadThreshold")))
	Alert(__("You are about to bump an old thread. This is usually a very bad idea. Please think about what you are about to do before you press the Post button."));


$attachs = array();

if (isset($_POST['saveuploads']))
{
	$attachs = HandlePostAttachments(0, false);
}
else if(isset($_POST['actionpreview']))
{
	$attachs = HandlePostAttachments(0, false);
	
	$previewPost['text'] = $_POST["text"];
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
	//Now check if the post is acceptable.
	$rejected = false;

	if(!trim($_POST['text']))
	{
		Alert(__("Enter a message and try again."), __("Your post is empty."));
		$rejected = true;
	}
	else if($thread['lastposter']==$loguserid && $thread['lastpostdate']>=time()-86400 && !HasPermission('user.doublepost'))
	{
		Alert(__("You can't double post until it's been at least one day."), __("Sorry"));
		$rejected = true;
	}
	else
	{
		$lastPost = time() - $loguser['lastposttime'];
		if($lastPost < Settings::get("floodProtectionInterval"))
		{
			//Check for last post the user posted.
			$lastPost = Fetch(Query("SELECT p.id,p.thread,pt.text FROM {posts} p LEFT JOIN {posts_text} pt ON pt.pid=p.id AND pt.revision=p.currentrevision 
				WHERE p.user={0} ORDER BY p.date DESC LIMIT 1", $loguserid));

			//If it looks similar to this one, assume the user has double-clicked the button.
			if($lastPost['thread'] == $tid && $lastPost['text'] == $_POST['text'])
			{
				$pid = $lastPost['id'];
				die(header("Location: ".actionLink("thread", 0, "pid=".$pid."#".$pid)));
			}

			$rejected = true;
			Alert(__("You're going too damn fast! Slow down a little."), __("Hold your horses."));
		}
	}

	if(!$rejected)
	{
		$ninja = FetchResult("select id from {posts} where thread={0} order by date desc limit 0, 1", $tid);
		if(isset($_POST['ninja']) && $_POST['ninja'] != $ninja)
		{
			Alert(__("You got ninja'd. You might want to review the post made while you were typing before you submit yours."));
			$rejected = true;
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

		if (HasPermission('mod.closethreads', $forum['id']))
		{
			if($_POST['lock'])
				$mod.= ", closed = 1";
			else if($_POST['unlock'])
				$mod.= ", closed = 0";
		}
		if (HasPermission('mod.stickthreads', $forum['id']))
		{
			if($_POST['stick'])
				$mod.= ", sticky = 1";
			else if($_POST['unstick'])
				$mod.= ", sticky = 0";
		}


		$now = time();

		$rUsers = Query("update {users} set posts=posts+1, lastposttime={0} where id={1} limit 1",
			time(), $loguserid);

		$rPosts = Query("insert into {posts} (thread, user, date, ip, num, options, mood) values ({0},{1},{2},{3},{4}, {5}, {6})",
			$tid, $loguserid, $now, $_SERVER['REMOTE_ADDR'], $loguser['posts']+1, $options, (int)$_POST['mood']);

		$pid = InsertId();

		$rPostsText = Query("insert into {posts_text} (pid,text,revision,user,date) values ({0}, {1}, {2}, {3}, {4})", $pid, $post, 0, $loguserid, time());

		$rFora = Query("update {forums} set numposts=numposts+1, lastpostdate={0}, lastpostuser={1}, lastpostid={2} where id={3} limit 1",
			$now, $loguserid, $pid, $fid);

		$rThreads = Query("update {threads} set lastposter={0}, lastpostdate={1}, replies=replies+1, lastpostid={2}".$mod." where id={3} limit 1",
			$loguserid, $now, $pid, $tid);
			
		$attachs = HandlePostAttachments($pid, true);
		Query("UPDATE {posts} SET has_attachments={0} WHERE id={1}", (!empty($attachs))?1:0, $pid);

		Report("New reply by [b]".$loguser['name']."[/] in [b]".$thread['title']."[/] (".$forum['title'].") -> [g]#HERE#?pid=".$pid, $isHidden);

		$bucket = "newreply"; include(BOARD_ROOT."lib/pluginloader.php");

		die(header("Location: ".actionLink("post", $pid)));
	}
	else
		$attachs = HandlePostAttachments(0, false);
}

$prefill = htmlspecialchars($_POST['text']);

if($_GET['quote'])
{
	$rQuote = Query("	select
					p.id, p.deleted, pt.text,
					t.forum fid, 
					u.name poster
				from {posts} p
					left join {posts_text} pt on pt.pid = p.id and pt.revision = p.currentrevision
					left join {threads} t on t.id=p.thread
					left join {users} u on u.id=p.user
				where p.id={0}", (int)$_GET['quote']);

	if(NumRows($rQuote))
	{
		$quote = Fetch($rQuote);

		//SPY CHECK!
		if (!HasPermission('forum.viewforum', $quote['fid']))
		{
			$quote['poster'] = 'your mom';
			$quote['text'] = __('Nice try kid, but no.');
		}
			
		if ($quote['deleted'])
			$quote['text'] = __('(deleted post)');

		$prefill = "[quote=\"".htmlspecialchars($quote['poster'])."\" id=\"".$quote['id']."\"]".htmlspecialchars($quote['text'])."[/quote]";
		$prefill = str_replace("/me", "[b]* ".htmlspecialchars(htmlspecialchars($quote['poster']))."[/b]", $prefill);
	}
}

function getCheck($name)
{
	if(isset($_POST[$name]) && $_POST[$name])
		return "checked=\"checked\"";
	else return "";
}

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

$ninja = FetchResult("select id from {posts} where thread={0} order by date desc limit 0, 1", $tid);

$mod_lock = '';
if (HasPermission('mod.closethreads', $fid))
{
	if(!$thread['closed'])
		$mod_lock = "<label><input type=\"checkbox\" ".getCheck("lock")." name=\"lock\">&nbsp;".__("Close thread", 1)."</label>\n";
	else
		$mod_lock = "<label><input type=\"checkbox\" ".getCheck("unlock")."  name=\"unlock\">&nbsp;".__("Open thread", 1)."</label>\n";
}

$mod_stick = '';
if (HasPermission('mod.stickthreads', $fid))
{
	if(!$thread['sticky'])
		$mod_stick = "<label><input type=\"checkbox\" ".getCheck("stick")."  name=\"stick\">&nbsp;".__("Sticky", 1)."</label>\n";
	else
		$mod_stick = "<label><input type=\"checkbox\" ".getCheck("unstick")."  name=\"unstick\">&nbsp;".__("Unstick", 1)."</label>\n";
}

$fields = array(
	'text' => "<textarea id=\"text\" name=\"text\" rows=\"16\">\n$prefill</textarea>",
	'mood' => "<select size=1 name=\"mood\">".$moodOptions."</select>",
	'nopl' => "<label><input type=\"checkbox\" ".getCheck('nopl')." name=\"nopl\">&nbsp;".__("Disable post layout", 1)."</label>",
	'nosm' => "<label><input type=\"checkbox\" ".getCheck('nosm')." name=\"nosm\">&nbsp;".__("Disable smilies", 1)."</label>",
	'lock' => $mod_lock,
	'stick' => $mod_stick,
	
	'btnPost' => "<input type=\"submit\" name=\"actionpost\" value=\"".__("Post")."\">",
	'btnPreview' => "<input type=\"submit\" name=\"actionpreview\" value=\"".__("Preview")."\">",
);

echo "
	<form name=\"postform\" action=\"".htmlentities(actionLink("newreply", $tid))."\" method=\"post\" enctype=\"multipart/form-data\">
		<input type=\"hidden\" name=\"ninja\" value=\"$ninja\">";
					
RenderTemplate('form_newreply', array('fields' => $fields));

PostAttachForm($attachs);

echo "
		</form>
	<script type=\"text/javascript\">
		document.postform.text.focus();
	</script>
";

doThreadPreview($tid);

