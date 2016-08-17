<?php
if (!defined('BLARG')) die();

// BBCode parser core.
// Parses BBCode and HTML intelligently so the output is reasonably well-formed, and doesn't contain evil stuff.

define('TAG_GOOD', 			0x0001);	// valid tag

define('TAG_BLOCK', 		0x0002);	// block tag (subject to newline removal after start/end tags)
define('TAG_SELFCLOSING',	0x0004);	// self-closing (br, img, ...)
define('TAG_CLOSEOPTIONAL',	0x0008);	// closing tag optional (tr, td, li, p, ...)
define('TAG_RAWCONTENTS',	0x0010);	// tag whose contents shouldn't be parsed (<style>)
define('TAG_NOAUTOLINK',	0x0020);	// prevent autolinking 
define('TAG_NOBR',			0x0040);	// no conversion of linebreaks to <br> (pre)

$TagList = array
(
	// HTML
	
	'<a'		=>	TAG_GOOD | TAG_NOAUTOLINK,
	'<abbr' 	=>  TAG_GOOD,
	'<acronym'  =>  TAG_GOOD,
	'<b'		=>	TAG_GOOD,
	'<big'		=>	TAG_GOOD,
	'<br'		=>	TAG_GOOD | TAG_SELFCLOSING,
	'<caption'	=>	TAG_GOOD | TAG_CLOSEOPTIONAL,
	'<center'	=>	TAG_GOOD,
	'<code'		=>	TAG_GOOD,
	'<dd'		=>	TAG_GOOD,
	'<del'		=>	TAG_GOOD,
	'<div'		=>	TAG_GOOD | TAG_BLOCK,
	'<dl'		=>	TAG_GOOD,
	'<dt'		=>	TAG_GOOD,
	'<em'		=>	TAG_GOOD,
	'<font'		=>	TAG_GOOD,
	'<h1'		=>	TAG_GOOD | TAG_BLOCK,
	'<h2'		=>	TAG_GOOD | TAG_BLOCK,
	'<h3'		=>	TAG_GOOD | TAG_BLOCK,
	'<h4'		=>	TAG_GOOD | TAG_BLOCK,
	'<h5'		=>	TAG_GOOD | TAG_BLOCK,
	'<h6'		=>	TAG_GOOD | TAG_BLOCK,
	'<hr'		=>	TAG_GOOD | TAG_SELFCLOSING,
	'<i'		=>	TAG_GOOD,
	'<img'		=>	TAG_GOOD | TAG_SELFCLOSING,
	'<input'	=>	TAG_GOOD | TAG_SELFCLOSING,
	'<kbd'		=>	TAG_GOOD,
	'<li'		=>	TAG_GOOD | TAG_BLOCK | TAG_CLOSEOPTIONAL,
	'<nobr'		=>	TAG_GOOD,
	'<ol'		=>	TAG_GOOD | TAG_BLOCK,
	'<p'		=>	TAG_GOOD | TAG_BLOCK | TAG_CLOSEOPTIONAL,
	'<pre'		=>	TAG_GOOD | TAG_BLOCK | TAG_NOBR,
	'<s'		=>	TAG_GOOD,
	'<small'	=>	TAG_GOOD,
	'<span'		=>	TAG_GOOD,
	'<strong'	=>	TAG_GOOD,
	'<style'	=>	TAG_GOOD | TAG_BLOCK | TAG_RAWCONTENTS,
	'<sub'		=>	TAG_GOOD,
	'<sup'		=>	TAG_GOOD,
	'<table'	=>	TAG_GOOD | TAG_BLOCK,
	'<tbody'	=>	TAG_GOOD | TAG_BLOCK | TAG_CLOSEOPTIONAL,
	'<td'		=>	TAG_GOOD | TAG_BLOCK | TAG_CLOSEOPTIONAL,
	'<textarea'	=>	TAG_GOOD | TAG_BLOCK | TAG_RAWCONTENTS,
	'<tfoot'	=>	TAG_GOOD | TAG_BLOCK | TAG_CLOSEOPTIONAL,
	'<th'		=>	TAG_GOOD | TAG_BLOCK | TAG_CLOSEOPTIONAL,
	'<thead'	=>	TAG_GOOD | TAG_BLOCK | TAG_CLOSEOPTIONAL,
	'<tr'		=>	TAG_GOOD | TAG_BLOCK | TAG_CLOSEOPTIONAL,
	'<u'		=>	TAG_GOOD,
	'<ul'		=>	TAG_GOOD | TAG_BLOCK,
	'<link'		=>	TAG_GOOD | TAG_BLOCK | TAG_SELFCLOSING,
	'<wbr' 		=>	TAG_GOOD | TAG_SELFCLOSING,
	
	'<audio'	=>	TAG_GOOD,
	'<video'	=>  TAG_GOOD,
	'<source'	=>  TAG_GOOD | TAG_SELFCLOSING,
	
	// BBCode
	
	'[b'		=>	TAG_GOOD,
	'[i'		=>	TAG_GOOD,
	'[u'		=>	TAG_GOOD,
	'[s'		=>	TAG_GOOD,
	
	'[url'		=>	TAG_GOOD | TAG_NOAUTOLINK,
	'[img'		=>	TAG_GOOD | TAG_RAWCONTENTS,
	'[imgs'		=>	TAG_GOOD | TAG_RAWCONTENTS,
	
	'[user'		=>	TAG_GOOD | TAG_SELFCLOSING,
	'[thread'	=>	TAG_GOOD | TAG_SELFCLOSING,
	'[forum'	=>	TAG_GOOD | TAG_SELFCLOSING,
	
	'[quote'	=>	TAG_GOOD | TAG_BLOCK,
	'[reply'	=>	TAG_GOOD | TAG_BLOCK,
	
	'[spoiler' 	=>	TAG_GOOD | TAG_BLOCK,
	'[code'		=>	TAG_GOOD | TAG_BLOCK | TAG_RAWCONTENTS,
	
	'[table'	=> 	TAG_GOOD | TAG_BLOCK,
	'[tr'		=> 	TAG_GOOD | TAG_BLOCK | TAG_CLOSEOPTIONAL,
	'[trh'		=> 	TAG_GOOD | TAG_BLOCK | TAG_CLOSEOPTIONAL,
	'[td'		=> 	TAG_GOOD | TAG_BLOCK | TAG_CLOSEOPTIONAL,
	
	'[youtube' 	=> 	TAG_GOOD | TAG_NOAUTOLINK,
);

$TagAllowedIn = array
(
	'<tbody'	=> array('<table' => 1),
	'<thead'	=> array('<table' => 1),
	'<tfoot'	=> array('<table' => 1),
	'<caption'	=> array('<table' => 1),
	'<colgroup'	=> array('<table' => 1),
	'<tr' 		=> array('<table' => 1, '<tbody' => 1, '<thead' => 1, '<tfoot' => 1),
	'<td'		=> array('<tr' => 1),
	'<th' 		=> array('<tr' => 1),
	'<col'		=> array('<colgroup' => 1),
	
	'<li' 		=> array('<ul' => 1, '<ol' => 1),
	
	
	'[tr'		=> array('[table' => 1),
	'[trh' 		=> array('[table' => 1),
	'[td'		=> array('[tr' => 1, '[trh' => 1),
);


function filterTag($tag, $attribs, $contents, $close, $parenttag)
{
	global $TagList, $bbcodeCallbacks;
	
	if ($tag[0] == '<')
	{
		$output = $tag.$attribs.$contents;
		// TODO filter attributes? (remove onclick etc)
		// this is done by the security filter, though, so it'd be redundant
		
		if ($close || !($TagList[$tag] & (TAG_CLOSEOPTIONAL | TAG_SELFCLOSING)))
			$output .= '</'.substr($tag,1).'>';
	}
	else
	{
		$attribs = substr($attribs,1,-1);
		$output = $bbcodeCallbacks[$tag]($contents, $attribs, $parenttag);
	}
	
	return $output;
}

function filterText($s, $parentTag, $parentMask)
{
	global $mobileLayout;
	
	if ($parentMask & TAG_RAWCONTENTS) return $s;
	
	// prevent unwanted shit
	$s = str_replace(array('<', '>'), array('&lt;', '&gt;'), $s);
	//$s = preg_replace('@&([a-z0-9]*[^a-z0-9;])@', '&amp;$1', $s);
	
	if (!($parentMask & TAG_NOBR)) $s = nl2br($s);
	$s = postDoReplaceText($s, $parentTag, $parentMask);
	return $s;
}

function tagAllowedIn($curtag, $parenttag)
{
	global $TagAllowedIn;
	
	if (!array_key_exists($curtag, $TagAllowedIn)) return true;
	return array_key_exists($parenttag, $TagAllowedIn[$curtag]);
}


function parseBBCode($text)
{
	global $TagList, $TagAllowedIn;
	$spacechars = array(' ', "\t", "\r", "\n", "\f");
	$attrib_bad = array(' ', "\t", "\r", "\n", "\f", '<', '[', '/', '=');
	
	$raw = preg_split("@(</?[a-zA-Z][^\s\f/>]*|\[/?[a-zA-Z][a-zA-Z0-9]*)@", $text, 0, PREG_SPLIT_DELIM_CAPTURE);
	$outputstack = array(0 => array('tag' => '', 'attribs' => '', 'contents' => ''));
	$si = 0;
	
	$currenttag = '';
	$currentmask = 0;

	$i = 0; $nraw = count($raw);
	while ($i < $nraw)
	{
		$rawcur = $raw[$i++];
		if ($rawcur[0] == '<' || $rawcur[0] == '[') // we got a tag start-- find out where it ends
		{
			$cur = strtolower($rawcur);
			$isclosing = $cur[1] == '/';
			$tagname = $cur[0].substr($cur, ($isclosing ? 2:1));
			$closechar = ($cur[0] == '<') ? '>' : ']';
			
			// raw contents tags (<style> & co)
			// continue outputting RAW content until we meet a matching closing tag
			if (($currentmask & TAG_RAWCONTENTS) && (!$isclosing || $currenttag != $tagname))
			{
				$outputstack[$si]['contents'] .= $rawcur;
				continue;
			}
			
			// invalid tag -- output it escaped
			$test = trim($raw[$i]);
			if (!array_key_exists($tagname, $TagList) || $test[0] == '<' || $test[0] == '[')
			{
				$outputstack[$si]['contents'] .= filterText(htmlspecialchars($rawcur), $currenttag, $currentmask);
				continue;
			}
			
			// we got a proper tag? find where it ends
			$tagmask = $TagList[$tagname];
			
			$next = $raw[$i++];
			
			$j = 0;
			$endfound = false;
			$inquote = false; $inattrib = ($cur[0]=='<')?0:1;
			for (;;)
			{
				$nlen = strlen($next);
				for (; $j < $nlen; $j++)
				{
					$ch = $next[$j];
					$isspace = in_array($ch, $spacechars);
					
					if (!$inquote)
					{
						if ($ch == $closechar)
						{
							$endfound = true;
							break;
						}
						
						if ($inattrib == 0 && !in_array($ch, $attrib_bad))
							$inattrib = 1;
						else if ($inattrib == 1)
						{
							if ($ch == '=')
								$inattrib = 2;
							else if (!$isspace)
								$inattrib = 0;
						}
						else if ($inattrib == 2)
						{
							if ($isspace)
								continue;
							
							if ($ch == '"' || $ch == '\'')
								$inquote = $ch;
							else
								$inquote = ' ';
						}
					}
					else if ($ch == $inquote || 
						($inquote == ' ' && $isspace))
					{
						$inquote = false;
						$inattrib = 0;
					}
					else if ($inquote == ' ' && $ch == $closechar)
					{
						$endfound = true;
						break;
					}
				}
				
				if ($endfound)
					break;
				
				if ($i >= $nraw)
					break;
				
				if ($j >= $nlen)
					$next .= $raw[$i++];
				else
					break;
			}
			
			if (!$endfound) // tag end not found-- call it invalid
				$outputstack[$si]['contents'] .= filterText(htmlspecialchars($rawcur.$next), $currenttag, $currentmask);
			else
			{
				$tagattribs = substr($next,0,$j+1);
				$followingtext = substr($next,$j+1);
				
				if ($tagmask & TAG_BLOCK)
					$followingtext = preg_replace("@^\r?\n@", '', $followingtext);
				
				if ($isclosing)
				{
					$tgood = false;
					
					// tag closing. Close any tags that need it before.
					
					$k = $si;
					while ($k > 0)
					{
						$closer = $outputstack[$k--];
						if ($closer['tag'] == $tagname)
						{
							$tgood = true;
							break;
						}
					}
					
					if ($tgood)
					{
						while ($si > 0)
						{
							$closer = $outputstack[$si--];
							$ccontents = $closer['contents'];
							$cattribs = $closer['attribs'];
							$ctag = $closer['tag'];
							$ctagname = substr($ctag,1);
							
							if ($ctag != $tagname)
								$outputstack[$si]['contents'] .= filterTag($ctag, $cattribs, $ccontents, false, $outputstack[$si]['tag']);
							else
								break;
						}
						
						$currenttag = $outputstack[$si]['tag'];
						$currentmask = $TagList[$currenttag];
						
						$outputstack[$si]['contents'] .= filterTag($ctag, $cattribs, $ccontents, true, $currenttag).filterText($followingtext, $currenttag, $currentmask);
					}
					else
						$outputstack[$si]['contents'] .= filterText(htmlspecialchars($followingtext), $currenttag, $currentmask);
				}
				else if ($tagmask & TAG_SELFCLOSING)
				{
					// self-closing tag (<br>, <img>, ...)
					
					$followingtext = filterText($followingtext, $currenttag, $currentmask);
					$outputstack[$si]['contents'] .= filterTag($cur, $tagattribs, '', false, $currenttag).$followingtext;
				}
				else
				{
					// tag opening. See if we need to close some tags before.
					
					if ($currentmask & TAG_CLOSEOPTIONAL)
					{
						$tgood = false;
						$k = $si;
						while ($k > 0)
						{
							$closer = $outputstack[$k--];
							if (tagAllowedIn($tagname, $closer['tag']))
							{
								$tgood = true;
								break;
							}
						}
						$k++;
						
						if ($tgood)
						{
							while ($si > $k)
							{
								$closer = $outputstack[$si--];
								$ccontents = $closer['contents'];
								$cattribs = $closer['attribs'];
								$ctag = $closer['tag'];
								$ctagname = substr($ctag,1);
								
								$outputstack[$si]['contents'] .= filterTag($ctag, $cattribs, $ccontents, false, $outputstack[$si]['tag']);
							}
							
							$outputstack[++$si] = array('tag' => $cur, 'attribs' => $tagattribs, 'contents' => filterText($followingtext, $cur, $tagmask));
						
							$currenttag = $cur;
							$currentmask = $tagmask;
						}
						else
							$outputstack[$si]['contents'] .= filterText(htmlspecialchars($followingtext), $currenttag, $currentmask);
					}
					else if (tagAllowedIn($tagname, $currenttag))
					{
						$outputstack[++$si] = array('tag' => $cur, 'attribs' => $tagattribs, 'contents' => filterText($followingtext, $cur, $tagmask));
						
						$currenttag = $cur;
						$currentmask = $tagmask;
					}
					else
						$outputstack[$si]['contents'] .= filterText(htmlspecialchars($followingtext), $currenttag, $currentmask);
				}
			}
		}
		else if ($rawcur)
			$outputstack[$si]['contents'] .= filterText($rawcur, $currenttag, $currentmask);
	}
	
	// close any leftover opened tags
	while ($si > 0)
	{
		$closer = $outputstack[$si--];
		$ccontents = $closer['contents'];
		$cattribs = $closer['attribs'];
		$ctag = $closer['tag'];
		
		if (!($TagList[$ctag] & TAG_SELFCLOSING))
			$outputstack[$si]['contents'] .= filterTag($ctag, $cattribs, $ccontents, false, $outputstack[$si]['tag']);
	}

	return $outputstack[$si]['contents'];
}

?>
