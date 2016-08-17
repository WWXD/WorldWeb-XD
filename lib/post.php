<?php
//  AcmlmBoard XD support - Post functions
if (!defined('BLARG')) die();

function ParseThreadTags($title)
{
	preg_match_all("/\[(.*?)\]/", $title, $matches);
	foreach($matches[1] as $tag)
	{
		$title = str_replace("[".$tag."]", "", $title);
		$tag = htmlspecialchars(strtolower($tag));

		//Start at a hue that makes "18" red.
		$hash = -105;
		for($i = 0; $i < strlen($tag); $i++)
			$hash += ord($tag[$i]);

		//That multiplier is only there to make "nsfw" and "18" the same color.
		$color = "hsl(".(($hash * 57) % 360).", 70%, 40%)";

		$tags .= "<span class=\"threadTag\" style=\"background-color: ".$color.";\">".$tag."</span>";
	}
	if($tags)
		$tags = " ".$tags;

	$title = str_replace("<", "&lt;", $title);
	$title = str_replace(">", "&gt;", $title);
	return array(trim($title), $tags);
}

function filterPollColors($input)
{
	return preg_replace("@[^#0123456789abcdef]@si", "", $input);
}

function loadBlockLayouts()
{
	global $blocklayouts, $loguserid;

	if(isset($blocklayouts))
		return;

	$rBlocks = Query("select * from {blockedlayouts} where blockee = {0}", $loguserid);
	$blocklayouts = array();

	while($block = Fetch($rBlocks))
		$blocklayouts[$block['user']] = 1;
}

function getSyndrome($activity)
{
	include(__DIR__."/syndromes.php");
	$soFar = "";
	foreach($syndromes as $minAct => $syndrome)
		if($activity >= $minAct)
			$soFar = "<em style=\"color: ".$syndrome[1].";\">".$syndrome[0]."</em><br>";
	return $soFar;
}

function applyTags($text, $tags)
{
	if(!stristr($text, "&"))
		return $text;
	$s = $text;
	foreach($tags as $tag => $val)
		$s = str_replace("&".$tag."&", $val, $s);
	if(is_numeric($tags['numposts']))
		$s = preg_replace('@&(\d+)&@sie', 'max($1 - '.$tags['numposts'].', 0)', $s);
	else
		$s = preg_replace("'&(\d+)&'si", "preview", $s);
	return $s;
}


$activityCache = array();
function getActivity($id)
{
	global $activityCache;

	if(!isset($activityCache[$id]))
		$activityCache[$id] = FetchResult("select count(*) from {posts} where user = {0} and date > {1}", $id, (time() - 86400));

	return $activityCache[$id];
}

function makePostText($post, $poster)
{
	$noSmilies = $post['options'] & 2;

	//Do Ampersand Tags
	$tags = array
	(
		"postnum" => $post['num'],
		"postcount" => $poster['posts'],
		"numdays" => floor((time()-$poster['regdate'])/86400),
		"date" => formatdate($post['date']),
		"rank" => GetRank($poster['rankset'], $poster['posts']),
	);
	$bucket = "amperTags"; include(__DIR__."/pluginloader.php");

	if($poster['signature'])
		if(!$poster['signsep'])
			$separator = "<br>_________________________<br>";
		else
			$separator = "<br>";
	
	$attachblock = '';
	if ($post['has_attachments'])
	{
		if (isset($post['preview_attachs']))
		{
			$ispreview = true;
			$fileids = array_keys($post['preview_attachs']);
			$attachs = Query("SELECT id,filename,physicalname,description,downloads 
				FROM {uploadedfiles}
				WHERE id IN ({0c})",
				$fileids);
		}
		else
		{
			$ispreview = false;
			$attachs = Query("SELECT id,filename,physicalname,description,downloads 
				FROM {uploadedfiles}
				WHERE parenttype={0} AND parentid={1} AND deldate=0
				ORDER BY filename",
				'post_attachment', $post['id']);
		}
		
		while ($attach = Fetch($attachs))
		{
			$url = URL_ROOT.'get.php?id='.htmlspecialchars($attach['id']);
			$linkurl = $ispreview ? '#' : $url;
			$filesize = filesize(DATA_DIR.'uploads/'.$attach['physicalname']);
			
			$attachblock .= '<br><div class="post_attachment">';
			
			$fext = strtolower(substr($attach['filename'], -4));
			if ($fext == '.png' || $fext == '.jpg' || $fext == 'jpeg' || $fext == '.gif')
			{
				$alt = htmlspecialchars($attach['filename']).' &mdash; '.BytesToSize($filesize).', viewed '.Plural($attach['downloads'], 'time');
				
				$attachblock .= '<a href="'.$linkurl.'"><img src="'.$url.'" alt="'.$alt.'" title="'.$alt.'" style="max-width:300px; max-height:300px;"></a>';
			}
			else
			{
				$link = '<a href="'.$linkurl.'">'.htmlspecialchars($attach['filename']).'</a>';
				
				$desc = htmlspecialchars($attach['description']);
				if ($desc) $desc .= '<br>';
				
				$attachblock .= '<strong>'.__('Attachment: ').$link.'</strong><br>';
				$attachblock .= '<div class="smallFonts">'.$desc;
				$attachblock .= BytesToSize($filesize).__(' &mdash; Downloaded ').Plural($attach['downloads'], 'time').'</div>';
			}
			
			$attachblock .= '</div>';
		}
	}

	$postText = $poster['postheader'].$post['text'].$attachblock.$separator.$poster['signature'];
	$postText = ApplyTags($postText, $tags);
	$postText = CleanUpPost($postText, $noSmilies, false);
	
	return $postText;
}

define('POST_NORMAL', 0);			// standard post box
define('POST_PM', 1);				// PM post box
define('POST_DELETED_SNOOP', 2);	// post box with close/undelete (for mods 'view deleted post' feature)
define('POST_SAMPLE', 3);			// sample post box (profile sample post, newreply post preview, etc)

// $post: post data (typically returned by SQL queries or forms)
// $type: one of the POST_XXX constants
// $params: an array of extra parameters, depending on the post box type. Possible parameters:
//		* tid: the ID of the thread the post is in (POST_NORMAL and POST_DELETED_SNOOP only)
//		* fid: the ID of the forum the thread containing the post is in (POST_NORMAL and POST_DELETED_SNOOP only)
// 		* threadlink: if set, a link to the thread is added next to 'Posted on blahblah' (POST_NORMAL and POST_DELETED_SNOOP only)
//		* noreplylinks: if set, no links to newreply.php (Quote/ID) are placed in the metabar (POST_NORMAL only)
function makePost($post, $type, $params=array())
{
	global $loguser, $loguserid, $usergroups, $isBot, $blocklayouts;
	
	$poster = getDataPrefix($post, 'u_');
	$post['userlink'] = UserLink($poster);
	
	LoadBlockLayouts();
	$pltype = Settings::get('postLayoutType');
	$isBlocked = $poster['globalblock'] || $loguser['blocklayouts'] || $post['options'] & 1 || isset($blocklayouts[$poster['id']]);
	
	$post['type'] = $type;
	$post['formattedDate'] = formatdate($post['date']);
	
	if (!HasPermission('admin.viewips')) $post['ip'] = '';
	else $post['ip'] = htmlspecialchars($post['ip']); // TODO IP formatting?

	if($post['deleted'] && $type == POST_NORMAL)
	{
		$post['deluserlink'] = UserLink(getDataPrefix($post, 'du_'));
		$post['delreason'] = htmlspecialchars($post['reason']);

		$links = array();
		if (HasPermission('mod.deleteposts', $params['fid']))
		{
			$links['undelete'] = actionLinkTag(__("Undelete"), "editpost", $post['id'], "delete=2&key=".$loguser['token']);
			$links['view'] = "<a href=\"#\" onclick=\"replacePost(".$post['id'].",true); return false;\">".__("View")."</a>";
		}
		$post['links'] = $links;
		
		RenderTemplate('postbox_deleted', array('post' => $post));
		return;
	}

	$links = array();

	if ($type != POST_SAMPLE)
	{
		$forum = $params['fid'];
		$thread = $params['tid'];
		
		$notclosed = (!$post['closed'] || HasPermission('mod.closethreads', $forum));
		
		$extraLinks = array();

		if (!$isBot)
		{
			if ($type == POST_DELETED_SNOOP)
			{
				if ($notclosed && HasPermission('mod.deleteposts', $forum))
					$links['undelete'] = actionLinkTag(__("Undelete"), "editpost", $post['id'], "delete=2&key=".$loguser['token']);
				
				$links['close'] = "<a href=\"#\" onclick=\"replacePost(".$post['id'].",false); return false;\">".__("Close")."</a>";
			}
			else if ($type == POST_NORMAL)
			{
				if ($notclosed)
				{
					if ($loguserid && HasPermission('forum.postreplies', $forum) && !$params['noreplylinks'])
						$links['quote'] = actionLinkTag(__("Quote"), "newreply", $thread, "quote=".$post['id']);

					$editrights = 0;
					if (($poster['id'] == $loguserid && HasPermission('user.editownposts')) || HasPermission('mod.editposts', $forum))
					{
						$links['edit'] = actionLinkTag(__("Edit"), "editpost", $post['id']);
						$editrights++;
					}
					
					if (($poster['id'] == $loguserid && HasPermission('user.deleteownposts')) || HasPermission('mod.deleteposts', $forum))
					{
						if ($post['id'] != $post['firstpostid'])
						{
							$link = htmlspecialchars(actionLink('editpost', $post['id'], 'delete=1&key='.$loguser['token']));
							$onclick = HasPermission('mod.deleteposts', $forum) ? 
								" onclick=\"deletePost(this);return false;\"" : ' onclick="if(!confirm(\'Really delete this post?\'))return false;"';
							$links['delete'] = "<a href=\"{$link}\"{$onclick}>".__('Delete')."</a>";
						}
						$editrights++;
					}
					
					if ($editrights < 2 && HasPermission('user.reportposts'))
						$links['report'] = actionLinkTag(__('Report'), 'reportpost', $post['id']);
				}
				
				// plugins should add to $extraLinks
				$bucket = "topbar"; include(__DIR__."/pluginloader.php");
			}
			
			$links['extra'] = $extraLinks;
		}

		//Threadlinks for listpost.php
		if ($params['threadlink'])
		{
			$thread = array();
			$thread['id'] = $post['thread'];
			$thread['title'] = $post['threadname'];
			$thread['forum'] = $post['fid'];

			$post['threadlink'] = makeThreadLink($thread);
		}
		else
			$post['threadlink'] = '';

		//Revisions
		if($post['revision'])
		{
			$ru_link = UserLink(getDataPrefix($post, "ru_"));
			$revdetail = ' '.format(__('by {0} on {1}'), $ru_link, formatdate($post['revdate']));

			if (HasPermission('mod.editposts', $forum))
				$post['revdetail'] = "<a href=\"javascript:void(0);\" onclick=\"showRevisions(".$post['id'].")\">".Format(__('rev. {0}'), $post['revision'])."</a>".$revdetail;
			else
				$post['revdetail'] = Format(__('rev. {0}'), $post['revision']).$revdetail;
		}
		//</revisions>
	}
	
	$post['links'] = $links;


	// POST SIDEBAR
	
	$sidebar = array();
	
	// quit abusing custom syndromes you unoriginal fuckers
	$poster['title'] = preg_replace('@Affected by \'?.*?Syndrome\'?@si', '', $poster['title']);

	$sidebar['rank'] = GetRank($poster['rankset'], $poster['posts']);

	if($poster['title'])
		$sidebar['title'] = strip_tags(CleanUpPost($poster['title'], '', true), '<b><strong><i><em><span><s><del><img><a><br/><br><small>');
	else
		$sidebar['title'] = htmlspecialchars($usergroups[$poster['primarygroup']]['title']);

	$sidebar['syndrome'] = GetSyndrome(getActivity($poster['id']));

	if($post['mood'] > 0)
	{
		if(file_exists(DATA_DIR."avatars/".$poster['id']."_".$post['mood']))
			$sidebar['avatar'] = "<img src=\"".DATA_URL."avatars/".$poster['id']."_".$post['mood']."\" alt=\"\">";
	}
	else if ($poster['picture'])
	{
		$pic = str_replace('$root/', DATA_URL, $poster['picture']);
		$sidebar['avatar'] = "<img src=\"".htmlspecialchars($pic)."\" alt=\"\">";
	}

	$lastpost = ($poster['lastposttime'] ? timeunits(time() - $poster['lastposttime']) : "none");
	$lastview = timeunits(time() - $poster['lastactivity']);

	if(!$post['num'])
		$sidebar['posts'] = $poster['posts'];
	else
		$sidebar['posts'] = $post['num'].'/'.$poster['posts'];

	$sidebar['since'] = cdate($loguser['dateformat'], $poster['regdate']);

	$sidebar['lastpost'] = $lastpost;
	$sidebar['lastview'] = $lastview;

	if($poster['lastactivity'] > time() - 300)
		$sidebar['isonline'] = __("User is <strong>online</strong>");
	
	$sidebarExtra = array();
	$bucket = "sidebar"; include(__DIR__."/pluginloader.php");
	$sidebar['extra'] = $sidebarExtra;
	
	$post['sidebar'] = $sidebar;

	// OTHER STUFF
	
	$post['haslayout'] = false;
	$post['fulllayout'] = false;
	
	if(!$isBlocked)
	{
		$poster['postheader'] = $pltype ? trim($poster['postheader']) : '';
		$poster['signature'] = trim($poster['signature']);
		
		$post['haslayout'] = $poster['postheader']?1:0;
		$post['fulllayout'] = $poster['fulllayout'] && $post['haslayout'] && ($pltype==2);
		
		if (!$post['haslayout'] && $poster['signature'])
			$poster['signature'] = '<div class="signature">'.$poster['signature'].'</div>';
	}
	else
	{
		$poster['postheader'] = '';
		$poster['signature'] = '';
	}

	$post['contents'] = makePostText($post, $poster);

	//PRINT THE POST!
	
	RenderTemplate('postbox', array('post' => $post));
}

?>
