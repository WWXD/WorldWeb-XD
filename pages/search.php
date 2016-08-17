<?php
if (!defined('BLARG')) die();

define('CACHE_TIME', 3600);

if(isset($_REQUEST['q']))
{
	$searchQuery = $_REQUEST['q'];
	$searchQuery = strtolower(preg_replace('@\s+@', ' ', $searchQuery));
	$sqhash = md5($searchQuery);
	
	$res = FetchResult("SELECT date FROM {searchcache} WHERE queryhash={0}", $sqhash);
	if ($res == -1 || $res < (time()-CACHE_TIME))
	{
		$bool = htmlspecialchars($searchQuery);

		$search = Query("
			SELECT t.id tid
			FROM {threads} t
			WHERE MATCH(t.title) AGAINST({0} IN BOOLEAN MODE)
			ORDER BY t.lastpostdate DESC", 
			$bool);

		$tresults = array();
		if(NumRows($search))
		{
			while($result = Fetch($search))
				$tresults[] = $result['tid'];
		}

		$search = Query("
			SELECT pt.pid pid
			FROM {posts_text} pt
				LEFT JOIN {posts} p ON pt.pid = p.id
			WHERE pt.revision = p.currentrevision AND MATCH(pt.text) AGAINST({0} IN BOOLEAN MODE)
			ORDER BY p.date DESC", 
			$bool);

		$presults = array();
		if(NumRows($search))
		{
			while($result = Fetch($search))
				$presults[] = $result['pid'];
		}
		
		Query("
			INSERT INTO {searchcache} (queryhash,query,date,threadresults,postresults) 
			VALUES ({0},{1},{2},{3},{4})
			ON DUPLICATE KEY UPDATE date={2}, threadresults={3}, postresults={4}",
			$sqhash, $searchQuery, time(), implode(',', $tresults), implode(',', $presults));
	}
	
	if (isset($_POST['q']))
		die(header('Location: '.actionLink('search', '', 'q='.urlencode($searchQuery).'&inposts='.$_POST['inposts'])));
}

MakeCrumbs(array(actionLink("search") => __("Search")));

echo "
	<form action=\"".htmlentities(actionLink("search"))."\" method=\"post\">";
	
$fields = array(
	'terms' => "<input type=\"text\" maxlength=\"1024\" name=\"q\" style=\"width:100%;border-sizing:border-box;-moz-border-sizing:border-box;\" value=\"".htmlspecialchars($_REQUEST['q'])."\">",
	'searchin' => '
		<label><input type="radio" name="inposts" value="0"'.($_REQUEST['inposts']==0 ? ' checked="checked"' : '').'>'.__('Thread titles').'</label> 
		<label><input type="radio" name="inposts" value="1"'.($_REQUEST['inposts']==1 ? ' checked="checked"' : '').'>'.__('Posts').'</label>',
	
	'btnSubmit' => "<input type=\"submit\" value=\"".__("Search")."\">",
);

RenderTemplate('form_search', array('fields' => $fields));

echo "
	</form>";


if(isset($_GET['q']))
{
	$viewableforums = ForumsWithPermission('forum.viewforum');
	
	$searchQuery = $_GET['q'];
	$searchQuery = strtolower(preg_replace('@\s+@', ' ', $searchQuery));
	
	$bool = htmlspecialchars($searchQuery);
	$t = explode(" ", $bool);
	$terms = array();
	foreach($t as $term)
	{
		if($term[0] == "-")
			continue;
		if($term[0] == "+" || $term[0] == "\"")
			$terms[] = substr($term, 1);
		else if($term[strlen($term)-1] == "*" || $term[strlen($term)-1] == "\"")
			$terms[] = substr($term, 0, strlen($term) - 1);
		else if($term != "")
			$terms[] = $term;
	}
	
	$res = Fetch(Query("SELECT ".($_GET['inposts']?'postresults':'threadresults')." AS results FROM {searchcache} WHERE queryhash={0}", md5($searchQuery)));
	$results = explode(',', $res['results']);
	$nres = 0;
	$rdata = array();
	
	if(isset($_GET['from'])) $from = (int)$_GET['from'];
	else $from = 0;
	$tpp = $loguser['threadsperpage'];
	if($tpp<1) $tpp=50;

	if (!$_GET['inposts'])
	{
		$nres = FetchResult("
			SELECT COUNT(*)
			FROM {threads} t
			WHERE t.id IN ({0c}) AND t.forum IN ({1c})", 
			$results, $viewableforums);
			
		$search = Query("
			SELECT
				t.id, t.title, t.user, t.lastpostdate, t.forum, 
				u.(_userfields)
			FROM {threads} t
				LEFT JOIN {users} u ON u.id=t.user
			WHERE t.id IN ({0c}) AND t.forum IN ({1c})
			ORDER BY t.lastpostdate DESC
			LIMIT {2u},{3u}", $results, $viewableforums, $from, $tpp);

		if(NumRows($search))
		{
			while($result = Fetch($search))
			{
				$r = array();
				
				$r['link'] = makeThreadLink($result);
				$r['description'] = '';
				
				$r['user'] = UserLink(getDataPrefix($result, "u_"));
				$r['formattedDate'] = formatdate($result['lastpostdate']);
				
				$rdata[] = $r;
			}
		}
	}
	else
	{
		$nres = FetchResult("
			SELECT COUNT(*)
			FROM {posts_text} pt
				LEFT JOIN {posts} p ON pt.pid = p.id
				LEFT JOIN {threads} t ON t.id = p.thread
			WHERE pt.pid IN ({0c}) AND t.forum IN ({1c}) AND pt.revision = p.currentrevision", 
			$results, $viewableforums);
			
		$search = Query("
			SELECT
				pt.text, pt.pid,
				p.date,
				t.title, t.id,
				u.(_userfields)
			FROM {posts_text} pt
				LEFT JOIN {posts} p ON pt.pid = p.id
				LEFT JOIN {threads} t ON t.id = p.thread
				LEFT JOIN {users} u ON u.id = p.user
			WHERE pt.pid IN ({0c}) AND t.forum IN ({1c}) AND pt.revision = p.currentrevision
			ORDER BY p.date DESC
			LIMIT {2u},{3u}", $results, $viewableforums, $from, $tpp);

		if(NumRows($search))
		{
			$results = "";
			while($result = Fetch($search))
			{
				$r = array();
				
				$tags = ParseThreadTags($result['title']);
				
	//			$result['text'] = str_replace("<!--", "~#~", str_replace("-->", "~#~", $result['text']));
				$r['description'] = MakeSnippet($result['text'], $terms);
				$r['user'] = UserLink(getDataPrefix($result, "u_"));
				$r['link'] = actionLinkTag($tags[0], "post", $result['pid']);
				$r['formattedDate'] = formatdate($result['date']);
				
				$rdata[] = $r;
			}
		}
	}
	
	if ($nres == 0) $restext = __('No results found');
	else if ($nres == 1) $restext = __('1 result found');
	else $restext = $nres.__(' results found');
	
	$pagelinks = PageLinks(actionLink('search', '', 'q='.urlencode($searchQuery).'&inposts='.$_GET['inposts'].'&from='), $tpp, $from, $nres);
	
	RenderTemplate('searchresults', array('results' => $rdata, 'nresults' => $nres, 'resultstext' => $restext, 'pagelinks' => $pagelinks));
}



function MakeSnippet($text, $terms, $title = false)
{
	$text = strip_tags($text);
	if(!$title)
		$text = preg_replace("/(\[\/?)(\w+)([^\]]*\])/i", "", $text);

	$lines = explode("\n", $text);
	$terms = implode("|", $terms);
	$contextlines = 3;
	$max = 50;
	$pat1 = "/(.*)(".$terms.")(.{0,".$max."})/i";
	$lineno = 0;
	$extract = "";
	foreach($lines as $line)
	{
		if($contextlines == 0)
			break;
		$lineno++;

		if($title)
			$line = htmlspecialchars($line);
		else
		{
			$m = array();
			if(!preg_match($pat1, $line, $m))
				continue;
			$contextlines--;

			$pre = substr($m[1], -$max);
			if(count($m) < 3)
				$post = "";
			else
				$post = $m[3];

			$found = $m[2];

			$line = htmlspecialchars($pre.$found.$post);
		}
		$line = trim($line);
		if($line == "")
			continue;
		$pat2 = "/(".$terms.")/i";
		$line = preg_replace($pat2, "<strong>\\1</strong>", $line);
		$line = preg_replace("/\~#\~(.*?)\~#\~/", "<span style=\"color: #6f6;\">&lt;!--\\1--&gt;</span>", $line);
		if(!$title)
			$extract .= "&bull; ".$line."<br />";
		else
			$extract .= $line;
	}

	return $extract;
}
