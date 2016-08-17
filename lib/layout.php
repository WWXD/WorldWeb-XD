<?php
if (!defined('BLARG')) die();

// ----------------------------------------------------------------------------
// --- General layout functions
// ----------------------------------------------------------------------------

function RenderTemplate($template, $options=null)
{
	global $tpl, $mobileLayout, $plugintemplates, $plugins;
	
	if (array_key_exists($template, $plugintemplates))
	{
		$plugin = $plugintemplates[$template];
		$self = $plugins[$plugin];
		
		$tplroot = __DIR__.'/../plugins/'.$self['dir'].'/templates/';
	}
	else
		$tplroot = __DIR__.'/../templates/';
	
	if ($mobileLayout)
	{
		$tplname = $tplroot.'mobile/'.$template.'.tpl';
		if (!file_exists($tplname)) 
			$tplname = $tplroot.$template.'.tpl';
	}
	else
		$tplname = $tplroot.$template.'.tpl';
	
	if ($options)
		$tpl->assign($options);
	
	$tpl->display($tplname);
}


function mfl_forumBlock($fora, $catid, $selID, $indent)
{
	$ret = '';
	
	foreach ($fora[$catid] as $forum)
	{
		$ret .=
'				<option value="'.$forum['id'].'"'.($forum['id'] == $selID ? ' selected="selected"':'').'>'
	.str_repeat('&nbsp; &nbsp; ', $indent).htmlspecialchars($forum['title'])
	.'</option>
';
		if (!empty($fora[-$forum['id']]))
			$ret .= mfl_forumBlock($fora, -$forum['id'], $selID, $indent+1);
	}
	
	return $ret;
}

function makeForumList($fieldname, $selectedID, $allowNone=false)
{
	global $loguserid, $loguser, $forumBoards;

	$viewableforums = ForumsWithPermission('forum.viewforum');
	$viewhidden = HasPermission('user.viewhiddenforums');
	
	$noneOption = '';
	if ($allowNone) $noneOption = '<option value=0>'.__('(none)').'</option>';
	
	$rCats = Query("SELECT id, name, board FROM {categories} ORDER BY board, corder, id");
	$cats = array();
	while ($cat = Fetch($rCats))
		$cats[$cat['id']] = $cat;

	$rFora = Query("	SELECT
							f.id, f.title, f.catid
						FROM
							{forums} f
						WHERE f.id IN ({0c})".(!$viewhidden ? " AND f.hidden=0" : '')." AND f.redirect=''
						ORDER BY f.forder, f.id", $viewableforums);
						
	$fora = array();
	while($forum = Fetch($rFora))
		$fora[$forum['catid']][] = $forum;

	$theList = '';
	foreach ($cats as $cid=>$cat)
	{
		if (empty($fora[$cid]))
			continue;
			
		$cname = $cat['name'];
		if ($cat['board']) $cname = $forumBoards[$cat['board']].' - '.$cname;
			
		$theList .= 
'			<optgroup label="'.htmlspecialchars($cname).'">
'.mfl_forumBlock($fora, $cid, $selectedID, 0).
'			</optgroup>
';
	}

	return "<select id=\"$fieldname\" name=\"$fieldname\">$noneOption$theList</select>";
}

function forumCrumbs($forum)
{
	global $forumBoards;
	$ret = array(actionLink('board') => __('Forums'));
	
	if ($forum['board'] != '')
		$ret[actionLink('board', $forum['board'])] = $forumBoards[$forum['board']];
	
	if (!$forum['id']) return $ret;
	
	$parents = Query("SELECT id,title FROM {forums} WHERE l<{0} AND r>{1} ORDER BY l", $forum['l'], $forum['r']);
	while ($p = Fetch($parents))
	{
		$public = HasPermission('forum.viewforum', $p['id'], true);
		$ret[actionLink('forum', $p['id'], '', $public?$p['title']:'')] = $p['title'];
	}
	
	$public = HasPermission('forum.viewforum', $forum['id'], true);
	$ret[actionLink('forum', $forum['id'], '', $public?$forum['title']:'')] = $forum['title'];
	return $ret;
}

function doThreadPreview($tid, $maxdate=0)
{
	global $loguser;
	
	$review = array();
	$ppp = $loguser['postsperpage'] ?: 20;
	
	$rPosts = Query("
		select
			{posts}.id, {posts}.date, {posts}.num, {posts}.deleted, {posts}.options, {posts}.mood, {posts}.ip,
			{posts_text}.text, {posts_text}.text, {posts_text}.revision,
			u.(_userfields), u.(posts)
		from {posts}
		left join {posts_text} on {posts_text}.pid = {posts}.id and {posts_text}.revision = {posts}.currentrevision
		left join {users} u on u.id = {posts}.user
		where thread={0} and deleted=0".($maxdate?' AND {posts}.date<={1}':'')."
		order by date desc limit 0, {2u}", $tid, $maxdate, $ppp);
		
	while ($post = Fetch($rPosts))
	{
		$pdata = array('id' => $post['id']);
		
		$poster = getDataPrefix($post, 'u_');
		$pdata['userlink'] = UserLink($poster);
		
		$pdata['posts'] = $post['num'].'/'.$poster['posts'];
		
		$nosm = $post['options'] & 2;
		$pdata['contents'] = CleanUpPost($post['text'], $poster['name'], $nosm);
		
		$review[] = $pdata;
	}
	
	RenderTemplate('threadreview', array('review' => $review));
}

function makeCrumbs($path, $links='')
{
	global $layout_crumbs, $layout_actionlinks;

	if(count($path) != 0)
	{
		$pathPrefix = array(actionLink(0) => Settings::get("breadcrumbsMainName"));

		$bucket = "breadcrumbs"; include(__DIR__."/pluginloader.php");

		$path = $pathPrefix + $path;
	}
	
	$layout_crumbs = $path;
	$layout_actionlinks = $links;
}


// parent=0: index listing
function makeForumListing($parent, $board='')
{
	global $loguserid, $loguser, $usergroups;
		
	$viewableforums = ForumsWithPermission('forum.viewforum');
	$viewhidden = HasPermission('user.viewhiddenforums');

	$rFora = Query("	SELECT f.*,
							c.name cname,
							".($loguserid ? "(NOT ISNULL(i.fid))" : "0")." ignored,
							(SELECT COUNT(*) FROM {threads} t".($loguserid ? " LEFT JOIN {threadsread} tr ON tr.thread=t.id AND tr.id={0}" : "")."
								WHERE t.forum=f.id AND t.lastpostdate>".($loguserid ? "IFNULL(tr.date,0)" : time()-900).") numnew,
							lu.(_userfields)
						FROM {forums} f
							LEFT JOIN {categories} c ON c.id=f.catid
							".($loguserid ? "LEFT JOIN {ignoredforums} i ON i.fid=f.id AND i.uid={0}" : "")."
							LEFT JOIN {users} lu ON lu.id=f.lastpostuser
						WHERE f.id IN ({1c}) AND ".($parent==0 ? 'c.board={2} AND f.catid>0' : 'f.catid={3}').(!$viewhidden ? " AND f.hidden=0" : '')."
						ORDER BY c.corder, c.id, f.forder, f.id", 
						$loguserid, $viewableforums, $board, -$parent);
	if (!NumRows($rFora))
		return;
		
	$f = Fetch(Query("SELECT MIN(l) minl, MAX(r) maxr FROM {forums} WHERE ".($parent==0 ? 'board={0}' : 'catid={1}'), $board, -$parent));
						
	$rSubfora = Query("	SELECT f.*,
							".($loguserid ? "(NOT ISNULL(i.fid))" : "0")." ignored,
							(SELECT COUNT(*) FROM {threads} t".($loguserid ? " LEFT JOIN {threadsread} tr ON tr.thread=t.id AND tr.id={0}" : "")."
								WHERE t.forum=f.id AND t.lastpostdate>".($loguserid ? "IFNULL(tr.date,0)" : time()-900).") numnew
						FROM {forums} f
							".($loguserid ? "LEFT JOIN {ignoredforums} i ON i.fid=f.id AND i.uid={0}" : "")."
						WHERE f.id IN ({1c}) AND f.l>{2} AND f.r<{3} AND f.catid!={4}".(!$viewhidden ? " AND f.hidden=0" : '')."
						ORDER BY f.forder, f.id", 
						$loguserid, $viewableforums, $f['minl'], $f['maxr'], -$parent);
	$subfora = array();
	while ($sf = Fetch($rSubfora))
		$subfora[-$sf['catid']][] = $sf;

	
	$rMods = Query("	SELECT 
							p.(arg, applyto, id),
							u.(_userfields)
						FROM
							{permissions} p
							LEFT JOIN {users} u ON p.applyto=1 AND p.id=u.id
						WHERE SUBSTR(p.perm,1,4)={0} AND p.arg!=0 AND p.value=1
						GROUP BY p.applyto, p.id, p.arg
						ORDER BY p.applyto, p.id",
						'mod.');
	$mods = array();
	while($mod = Fetch($rMods))
		$mods[$mod['p_arg']][] = $mod['p_applyto'] ? getDataPrefix($mod, "u_") : array('groupid' => $mod['p_id']);

	$categories = array();
	while($forum = Fetch($rFora))
	{
		$skipThisOne = false;
		$bucket = "forumListMangler"; include(__DIR__."/pluginloader.php");
		if($skipThisOne)
			continue;
			
		if (!$categories[$forum['catid']])
			$categories[$forum['catid']] = array('id' => $forum['catid'], 'name' => ($parent==0)?$forum['cname']:'Subforums', 'forums' => array());
			
		$fdata = array('id' => $forum['id']);
			
		if ($forum['redirect'])
		{
			$redir = $forum['redirect'];
			if ($redir[0] == ':')
			{
				$redir = explode(':', $redir);
				$fdata['link'] = actionLinkTag($forum['title'], $redir[1], $redir[2], $redir[3], $redir[4]);
				$forum['numthreads'] = '-';
				$forum['numposts'] = '-';
				
				if ($redir[1] == 'board')
				{
					$tboard = $redir[2];
					$f = Fetch(Query("SELECT MIN(l) minl, MAX(r) maxr FROM {forums} WHERE board={0}", $tboard));
					
					$forum['numthreads'] = 0;
					$forum['numposts'] = 0;
					$sforums = Query("	SELECT f.id, f.numthreads, f.numposts, f.lastpostid, f.lastpostuser, f.lastpostdate,
											".($loguserid ? "(NOT ISNULL(i.fid))" : "0")." ignored,
											(SELECT COUNT(*) FROM {threads} t".($loguserid ? " LEFT JOIN {threadsread} tr ON tr.thread=t.id AND tr.id={0}" : "")."
												WHERE t.forum=f.id AND t.lastpostdate>".($loguserid ? "IFNULL(tr.date,0)" : time()-900).") numnew,
											lu.(_userfields)
										FROM {forums} f
											".($loguserid ? "LEFT JOIN {ignoredforums} i ON i.fid=f.id AND i.uid={0}" : "")."
											LEFT JOIN {users} lu ON lu.id=f.lastpostuser
										WHERE f.l>={1} AND f.r<={2}", 
										$loguserid, $f['minl'], $f['maxr']);
					while ($sforum = Fetch($sforums))
					{
						$forum['numthreads'] += $sforum['numthreads'];
						$forum['numposts'] += $sforum['numposts'];
						
						if (!HasPermission('forum.viewforum', $sforum['id']))
							continue;
						
						if (!$sforum['ignored'])
							$forum['numnew'] += $sforum['numnew'];
						
						if ($sforum['lastpostdate'] > $forum['lastpostdate'])
						{
							$forum['lastpostdate'] = $sforum['lastpostdate'];
							$forum['lastpostid'] = $sforum['lastpostid'];
							$forum['lastpostuser'] = $sforum['lastpostuser'];
							foreach ($sforum as $key=>$val)
							{
								if (substr($key,0,3) != 'lu_') continue;
								$forum[$key] = $val;
							}
						}
					}
				}
			}
			else
				$fdata['link'] = '<a href="'.htmlspecialchars($redir).'">'.$forum['title'].'</a>';
		}
		else
			$fdata['link'] = actionLinkTag($forum['title'], "forum",  $forum['id'], '', 
				HasPermission('forum.viewforum', $forum['id'], true) ? $forum['title'] : '');
				
		$fdata['ignored'] = $forum['ignored'];

		$newstuff = 0;
		$localMods = '';
		$subforaList = '';

		$newstuff = $forum['ignored'] ? 0 : $forum['numnew'];
		if ($newstuff > 0)
			$fdata['new'] = "<div class=\"statusIcon new\">$newstuff</div>";
			
		$fdata['description'] = $forum['description'];

		if (isset($mods[$forum['id']]))
		{
			foreach($mods[$forum['id']] as $user)
			{
				if ($user['groupid'])
					$localMods .= htmlspecialchars($usergroups[$user['groupid']]['name']).', ';
				else
					$localMods .= UserLink($user).', ';
			}
		}

		if($localMods)
			$fdata['localmods'] = substr($localMods,0,-2);
			
		if (isset($subfora[$forum['id']]))
		{
			foreach ($subfora[$forum['id']] as $subforum)
			{
				$link = actionLinkTag($subforum['title'], 'forum', $subforum['id'], '', 
					HasPermission('forum.viewforum', $subforum['id'], true) ? $subforum['title'] : '');
				
				if ($subforum['ignored'])
					$link = '<span class="ignored">'.$link.'</span>';
				else if ($subforum['numnew'] > 0)
					$link = '<div class="statusIcon new"></div> '.$link;
					
				$subforaList .= $link.', ';
			}
		}
			
		if($subforaList)
			$fdata['subforums'] = substr($subforaList,0,-2);
			
		$fdata['threads'] = $forum['numthreads'];
		$fdata['posts'] = $forum['numposts'];

		if($forum['lastpostdate'])
		{
			$user = getDataPrefix($forum, "lu_");

			$fdata['lastpostdate'] = formatdate($forum['lastpostdate']);
			$fdata['lastpostuser'] = UserLink($user);
			$fdata['lastpostlink'] = actionLink('post', $forum['lastpostid']);
		}
		else
			$fdata['lastpostdate'] = 0;
			
		$categories[$forum['catid']]['forums'][$forum['id']] = $fdata;
	}
	
	RenderTemplate('forumlist', array('categories' => $categories));
}

function makeThreadListing($threads, $pagelinks, $dostickies = true, $showforum = false)
{
	global $loguserid, $loguser, $misc;

	$threadlist = array();
	while ($thread = Fetch($threads))
	{
		$tdata = array('id' => $thread['id']);
		$starter = getDataPrefix($thread, 'su_');
		$last = getDataPrefix($thread, 'lu_');
		
		$ispublic = HasPermission('forum.viewforum', $thread['forum'], true);
		$tags = ParseThreadTags($thread['title']);
		$urlname = $ispublic ? $tags[0] : '';

		$threadlink = actionLinkTag($tags[0], 'thread', $thread['id'], '', $urlname);
		$tdata['link'] = (Settings::get("tagsDirection") === 'Left') ? $tags[1].' '.$threadlink : $threadlink.' '.$tags[1];


		$NewIcon = '';
		$tdata['gotonew'] = '';
		
		if($thread['closed'])
			$NewIcon = 'off';
		if($thread['replies'] >= $misc['hotcount'])
			$NewIcon .= 'hot';
		if((!$loguserid && $thread['lastpostdate'] > time() - 900) ||
			($loguserid && $thread['lastpostdate'] > $thread['readdate']))
		{
			$NewIcon .= 'new';
			if ($loguserid)
			{
				$tdata['gotonew'] = actionLinkTag('<img src="'.resourceLink('img/gotounread.png').'" alt="[go to first unread post]">',
					'post', '', 'tid='.$thread['id'].'&time='.(int)$thread['readdate']);
			}
		}
		else if(!$thread['closed'] && !$thread['sticky'] && Settings::get("oldThreadThreshold") > 0 && $thread['lastpostdate'] < time() - (2592000 * Settings::get("oldThreadThreshold")))
			$NewIcon = 'old';

		if($NewIcon)
			$tdata['new'] = '<div class="statusIcon '.$NewIcon.'"></div>';
		else
			$tdata['new'] = '';
			
		$tdata['sticky'] = $thread['sticky'];

		if($thread['icon'])
		{
			//This is a hack, but given how icons are stored in the DB, I can do nothing about it without breaking DB compatibility.
			if(startsWith($thread['icon'], "img/"))
				$thread['icon'] = resourceLink($thread['icon']);
			$tdata['icon'] = "<img src=\"".htmlspecialchars($thread['icon'])."\" alt=\"\" class=\"smiley\" style=\"max-width:32px; max-height:32px;\">";
		}
		else
			$tdata['icon'] = '';

		$tdata['poll'] = ($thread['poll'] ? "<img src=\"".resourceLink("img/poll.png")."\" alt=\"[poll]\">" : "");


		$n = 4;
		$total = $thread['replies'];

		$ppp = $loguser['postsperpage'];
		if(!$ppp) $ppp = 20;

		$numpages = floor($total / $ppp);
		$pl = '';
		if($numpages <= $n * 2)
		{
			for($i = 1; $i <= $numpages; $i++)
				$pl .= " ".actionLinkTag($i+1, "thread", $thread['id'], "from=".($i * $ppp), $urlname);
		}
		else
		{
			for($i = 1; $i < $n; $i++)
			$pl .= " ".actionLinkTag($i+1, "thread", $thread['id'], "from=".($i * $ppp), $urlname);
			$pl .= " &hellip; ";
			for($i = $numpages - $n + 1; $i <= $numpages; $i++)
				$pl .= " ".actionLinkTag($i+1, "thread", $thread['id'], "from=".($i * $ppp), $urlname);
		}
		if($pl)
			$tdata['pagelinks'] = actionLinkTag(1, "thread", $thread['id'], '', $urlname).$pl;
		else
			$tdata['pagelinks'] = '';
			
		if ($showforum)
			$tdata['forumlink'] = actionLinkTag(htmlspecialchars($thread["f_title"]), "forum", $thread["f_id"], "", $ispublic?$thread["f_title"]:'');
			
		$tdata['startuser'] = UserLink($starter);
		
		$tdata['replies'] = $thread['replies'];
		$tdata['views'] = $thread['views'];

		$tdata['lastpostdate'] = formatdate($thread['lastpostdate']);
		$tdata['lastpostuser'] = UserLink($last);
		$tdata['lastpostlink'] = actionLink("post", $thread['lastpostid']);
		
		$threadlist[$tdata['id']] = $tdata;
	}
	
	RenderTemplate('threadlist', array('threads' => $threadlist, 'pagelinks' => $pagelinks, 'dostickies' => $dostickies, 'showforum' => $showforum));
}

function makeAnncBar()
{
	global $loguserid;
	
	$anncforum = Settings::get('anncForum');
	if ($anncforum > 0)
	{
		$annc = Query("	SELECT 
							t.id, t.title, t.icon, t.poll, t.forum,
							t.date anncdate,
							".($loguserid ? "tr.date readdate," : '')."
							u.(_userfields)
						FROM 
							{threads} t 
							".($loguserid ? "LEFT JOIN {threadsread} tr ON tr.thread=t.id AND tr.id={1}" : '')."
							LEFT JOIN {users} u ON u.id=t.user
						WHERE forum={0}
						ORDER BY anncdate DESC LIMIT 1", $anncforum, $loguserid);
								
		if ($annc && NumRows($annc))
		{
			$annc = Fetch($annc);
			$adata = array();
			
			$adata['new'] = '';
			if ((!$loguserid && $annc['anncdate'] > (time()-900)) ||
				($loguserid && $annc['anncdate'] > $annc['readdate']))
				$adata['new'] = "<div class=\"statusIcon new\"></div>";
			
			$adata['poll'] = ($annc['poll'] ? "<img src=\"".resourceLink('img/poll.png')."\" alt=\"Poll\"/> " : '');
			$adata['link'] = MakeThreadLink($annc);
			
			$user = getDataPrefix($annc, 'u_');
			$adata['user'] = UserLink($user);
			$adata['date'] = formatdate($annc['anncdate']);
			
			RenderTemplate('anncbar', array('annc' => $adata));
		}
	}
}

?>
