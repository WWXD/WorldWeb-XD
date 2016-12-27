<?php

define('WIKIXD', 1);

define('WIKI_PFLAG_DELETED', 1);
define('WIKI_PFLAG_SPECIAL', 2);
define('WIKI_PFLAG_NOCONTBOX', 4);

function title2url($str)
{
	return preg_replace('@\s+@s', '_', $str);
}

function url2title($str)
{
	return str_replace('_', ' ', $str);
}

function getWikiPage($id, $rev = 0)
{
	global $canedit, $canmod;
	
	$ptitle = $id;
	if (!$ptitle) $ptitle = 'Main_page';
	else $ptitle = title2url($ptitle); // so that we don't have for example 'Main page' and 'Main_page' being considered different pages
	
	if ($rev < 0) $rev = 0;

	$page = Query("SELECT p.*, pt.date, pt.user, pt.text FROM {wiki_pages} p LEFT JOIN {wiki_pages_text} pt ON pt.id=p.id AND pt.revision=".($rev>0 ? 'LEAST(p.revision,{1})':'p.revision')." WHERE p.id={0}", 
		$ptitle, $rev);
	if (!NumRows($page))
	{
		$page = array(
			'id' => $ptitle,
			'revision' => 0,
			'flags' => 0,
			'text' => '',
			'new' => 1
		);

		header('HTTP/1.1 404 Not Found');
		header('Status: 404 Not Fount');
	}
	else
		$page = Fetch($page);
	
	$page['istalk'] = (strtolower(substr($ptitle,0,5)) == 'talk:');
	$page['ismain'] = (strtolower($ptitle) == 'main_page');
	$page['canedit'] = $canedit && ((!($page['flags'] & WIKI_PFLAG_SPECIAL)) || HasPermission('wiki.makepagesspecial'));
	
	return $page;
}

function headingHandler($h)
{
	global $hlevels, $contentsbox;
	
	$tag = (int)substr($h[1],1);
	$level = $hlevels['cur'];
	if ($tag != $hlevels['curtag'])
	{
		if ($hlevels['curtag'] != -1)
		{
			if ($tag > $hlevels['curtag'])
			{
				$hlevels['t'.$hlevels['curtag']] = $level;
				$level++;
				$hlevels[$level] = 1;
			}
			else
			{
				$level = $hlevels['t'.$tag];
				$hlevels[$level]++;
			}
		}
		else
		{
			$hlevels[$level] = 1;
		}
		
		$hlevels['cur'] = $level;
		$hlevels['curtag'] = $tag;
	}
	else $hlevels[$level]++;
	
	$htext = '';
	for ($i = 0; $i <= $level; $i++) $htext .= $hlevels[$i].'.';
	$htext .= ' '.$h[2];
	$htext_uf = title2url($h[2]);
	
	$contentsbox .= str_repeat('&nbsp; &nbsp; ', $level).'<a href="#'.urlencode($htext_uf).'">'.$htext.'</a><br>';
	
	return '<'.$h[1].' id="'.htmlspecialchars($htext_uf).'">'.$htext.'</'.$h[1].'>';
}

function makeLink($m)
{
	return actionLinkTag(htmlspecialchars(url2title($m[1])), 'wiki', title2url($m[1]));
}

function makeNiceLink($m)
{
	return actionLinkTag(htmlspecialchars($m[2]), 'wiki', title2url($m[1]));
}

function makeList($m)
{
	$elem = trim($m[0]);
	$elem = substr($elem, 1);
	$elem = trim($elem);
	
	return '[li]'.$elem.'[/li]';
}

function finalizeList($m)
{
	print_r($m);
	
	return $m[0];
}

function wikiFilter($text, $nocontbox)
{
	global $hlevels, $contentsbox;
	
	// get rid of those annoying \r's once for all
	$text = str_replace("\r", '', $text);
	
	// special wiki markup
	// gets processed before we run the post parser and its security filters -- we never know
	$text = preg_replace_callback('@\[\[([^\]]+?)\|([\w\s]+?)\]\]@s', 'makeNiceLink', $text);
	$text = preg_replace_callback('@\[\[([^\]]+?)\]\]@s', 'makeLink', $text);
	
	//$text = preg_replace_callback('@^\s*\*\s*.+?$@m', 'makeList', $text);
	//$text = preg_replace_callback("@(\[li\](.+?)\[/li\]\n)+@", 'finalizeList', $text);
	$text = preg_replace('@^[ ]*\*[ ]*(.+?)$@m', '&bull; $1', $text);
	
	// run the post parser on it and call it good
	$text = CleanUpPost($text, '', false, false);
	
	$hlevels = array('cur'=>0, 'curtag'=>-1);
	$contentsbox = '';
	$text = preg_replace_callback('@^<(h[1-6]).*?>(.+?)</\1.*?>$@mi', 'headingHandler', $text);
	
	if ($contentsbox && (!$nocontbox)) $text = '
		<table class="outline margin" style="display:inline-block;width:auto;">
			<tr class="header1"><th>Contents</th></tr>
			<tr class="cell0">
				<td style="padding:1em;">
					'.$contentsbox.'
				</td>
			</tr>
		</table>
		<br>
		'.$text;
	
	return $text;
}


// TODO: implement wiki banning/localmod system
$canedit = $loguserid && HasPermission('wiki.editpages');
$token = hash('sha256', "{$loguserid},{$loguser['pss']},".SALT.",wikiXD,sfg657gsfh685gh7s4sg6f5hgf");

$links = actionLinkTagItem('Recent changes', 'wikichanges');
if ($canedit) $links .= actionLinkTagItem('Create page', 'wikiedit', '', 'createnew');

?>