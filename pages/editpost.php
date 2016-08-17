<?php
//  AcmlmBoard XD - Post editing page
//  Access: users
if (!defined('BLARG')) die();

require(BOARD_ROOT.'lib/upload.php');

$title = __("Edit post");

if(!$loguserid)
	Kill(__("You must be logged in to edit your posts."));

$pid = (int)$_REQUEST['id'];

$rPost = Query("
	SELECT
		{posts}.*,
		{posts_text}.text
	FROM {posts}
		LEFT JOIN {posts_text} ON {posts_text}.pid = {posts}.id AND {posts_text}.revision = {posts}.currentrevision
	WHERE id={0}", $pid);

if(NumRows($rPost))
{
	$post = Fetch($rPost);
	$tid = $post['thread'];
}
else
	Kill(__("Unknown post ID."));

$rUser = Query("select * from {users} where id={0}", $post['user']);
if(NumRows($rUser))
	$user = Fetch($rUser);
else
	Kill(__("Unknown user ID."));

$rThread = Query("select * from {threads} where id={0}", $tid);
if(NumRows($rThread))
	$thread = Fetch($rThread);
else
	Kill(__("Unknown thread ID."));

$rFora = Query("select * from {forums} where id={0}", $thread['forum']);
if(NumRows($rFora))
	$forum = Fetch($rFora);
else
	Kill(__("Unknown forum ID."));
	
if (!HasPermission('forum.viewforum', $forum['id']))
	Kill(__('You may not access this forum.'));

$fid = $forum['id'];
$OnlineUsersFid = $fid;

$isHidden = !HasPermission('forum.viewforum', $forum['id'], true);

$isFirstPost = ($thread['firstpostid'] == $post['id']);
$isLastPost = ($thread['lastpostid'] == $post['id']);

if($thread['closed'] && !HasPermission('mod.closethreads', $fid))
	Kill(__("This thread is closed."));

if((int)$_GET['delete'] == 1)
{
	if ($_GET['key'] != $loguser['token']) Kill(__("No."));
	
	if ($isFirstPost)
		Kill(__("You may not delete a thread's first post."));
	
	if(!HasPermission('mod.deleteposts', $fid))
	{
		if ($post['user'] != $loguserid || !HasPermission('user.deleteownposts'))
			Kill(__("You are not allowed to delete this post."));
		
		$_GET['reason'] = '';
	}
	$rPosts = Query("update {posts} set deleted=1,deletedby={0},reason={1} where id={2} limit 1", $loguserid, $_GET['reason'], $pid);

	die(header("Location: ".actionLink("post", $pid)));
}
else if((int)$_GET['delete'] == 2)
{
	if ($_GET['key'] != $loguser['token']) Kill(__("No."));
	
	if(!HasPermission('mod.deleteposts', $fid))
		Kill(__("You're not allowed to undelete posts."));
	$rPosts = Query("update {posts} set deleted=0 where id={0} limit 1", $pid);

	die(header("Location: ".actionLink("post", $pid)));
}

if ($post['deleted'])
	Kill(__("This post has been deleted."));

if(($post['user'] != $loguserid || !HasPermission('user.editownposts')) && !HasPermission('mod.editposts', $fid))
	Kill(__("You are not allowed to edit this post."));

$tags = ParseThreadTags($thread['title']);
MakeCrumbs(forumCrumbs($forum) + array(actionLink("thread", $tid, '', $isHidden?'':$tags[0]) => $tags[0], '' => __("Edit post")));

LoadPostToolbar();

$attachs = array();
if ($post['has_attachments'])
{
	$res = Query("SELECT id,filename 
		FROM {uploadedfiles}
		WHERE parenttype={0} AND parentid={1} AND deldate=0
		ORDER BY filename",
		'post_attachment', $pid);
	while ($a = Fetch($res))
		$attachs[$a['id']] = $a['filename'];
}

if (isset($_POST['saveuploads']))
{
	$attachs = HandlePostAttachments(0, false);
}
else if(isset($_POST['actionpreview']))
{
	$attachs = HandlePostAttachments(0, false);
	
	$previewPost['text'] = $_POST['text'];
	$previewPost['num'] = $post['num'];
	$previewPost['id'] = 0;
	$previewPost['options'] = 0;
	if($_POST['nopl']) $previewPost['options'] |= 1;
	if($_POST['nosm']) $previewPost['options'] |= 2;
	$previewPost['mood'] = (int)$_POST['mood'];
	$previewPost['has_attachments'] = !empty($attachs);
	$previewPost['preview_attachs'] = $attachs;
	
	foreach($user as $key => $value)
		$previewPost['u_'.$key] = $value;
	MakePost($previewPost, POST_SAMPLE);
}
else if(isset($_POST['actionpost']))
{
	if ($_POST['key'] != $loguser['token']) Kill(__("No."));

	$rejected = false;

	if(!trim($_POST['text']))
	{
		Alert(__("Enter a message and try again."), __("Your post is empty."));
		$rejected = true;
	}

	if(!$rejected)
	{
		$bucket = "checkPost"; include(BOARD_ROOT."lib/pluginloader.php");
	}

	if(!$rejected)
	{
		$options = 0;
		if($_POST['nopl']) $options |= 1;
		if($_POST['nosm']) $options |= 2;

		if ($_POST['text'] != $post['text'])
		{
			$now = time();
			$rev = fetchResult("select max(revision) from {posts_text} where pid={0}", $pid);
			$rev++;
			Query("insert into {posts_text} (pid,text,revision,user,date) values ({0}, {1}, {2}, {3}, {4})",
								$pid, $_POST['text'], $rev, $loguserid, $now);

			Query("update {posts} set options={0}, mood={1}, currentrevision = currentrevision + 1 where id={2} limit 1",
							$options, (int)$_POST['mood'], $pid);

			// mark the thread as new if we edited the last post
			// all we have to do is update the thread's lastpostdate
			if($isLastPost)
			{
				Query("UPDATE {threads} SET lastpostdate={0} WHERE id={1}", $now, $thread['id']);
				Query("UPDATE {forums} SET lastpostdate={0} WHERE id={1}", $now, $fid);
			}
		}
		else
			Query("update {posts} set options={0}, mood={1} where id={2} limit 1",
							$options, (int)$_POST['mood'], $pid);
							
		$attachs = HandlePostAttachments($pid, true);
		Query("UPDATE {posts} SET has_attachments={0} WHERE id={1}", (!empty($attachs))?1:0, $pid);

		Report("Post edited by [b]".$loguser['name']."[/] in [b]".$thread['title']."[/] (".$forum['title'].") -> [g]#HERE#?pid=".$pid, $isHidden);
		$bucket = 'editpost'; include(BOARD_ROOT."lib/pluginloader.php");

		die(header("Location: ".actionLink("post", $pid)));
	}
	else
		$attachs = HandlePostAttachments(0, false);
}

if(isset($_POST['actionpreview']) || isset($_POST['actionpost']))
{
	$prefill = $_POST['text'];
	if($_POST['nopl']) $nopl = "checked=\"checked\"";
	if($_POST['nosm']) $nosm = "checked=\"checked\"";
}
else
{
	$prefill = $post['text'];
	if($post['options'] & 1) $nopl = "checked=\"checked\"";
	if($post['options'] & 2) $nosm = "checked=\"checked\"";
	$_POST['mood'] = $post['mood'];
}

$moodSelects = array();
if($_POST['mood'])
	$moodSelects[(int)$_POST['mood']] = "selected=\"selected\" ";
$moodOptions = Format("<option {0}value=\"0\">".__("[Default avatar]")."</option>\n", $moodSelects[0]);
$rMoods = Query("select mid, name from {moodavatars} where uid={0} order by mid asc", $post['user']);
while($mood = Fetch($rMoods))
	$moodOptions .= Format("<option {0}value=\"{1}\">{2}</option>\n", $moodSelects[$mood['mid']], $mood['mid'], htmlspecialchars($mood['name']));
	
$fields = array(
	'text' => "<textarea id=\"text\" name=\"text\" rows=\"16\">\n".htmlspecialchars($prefill)."</textarea>",
	'mood' => "<select size=1 name=\"mood\">".$moodOptions."</select>",
	'nopl' => "<label><input type=\"checkbox\" $nopl name=\"nopl\">&nbsp;".__("Disable post layout", 1)."</label>",
	'nosm' => "<label><input type=\"checkbox\" $nosm name=\"nosm\">&nbsp;".__("Disable smilies", 1)."</label>",
	
	'btnPost' => "<input type=\"submit\" name=\"actionpost\" value=\"".__("Save")."\">",
	'btnPreview' => "<input type=\"submit\" name=\"actionpreview\" value=\"".__("Preview")."\">",
);

echo "
	<form name=\"postform\" action=\"".htmlentities(actionLink("editpost", $pid))."\" method=\"post\" enctype=\"multipart/form-data\">";

RenderTemplate('form_editpost', array('fields' => $fields));

PostAttachForm($attachs);

echo "
		<input type=\"hidden\" name=\"key\" value=\"{$loguser['token']}\">
	</form>
	<script type=\"text/javascript\">
		document.postform.text.focus();
	</script>
";

doThreadPreview($tid, $post['date']);

?>