<?php
if (!defined('BLARG')) die();

$bbcodeCallbacks = array
(
	"[b" => "bbcodeBold",
	"[i" => "bbcodeItalics",
	"[u" => "bbcodeUnderline",
	"[s" => "bbcodeStrikethrough",

	"[url" => "bbcodeURL",
	"[img" => "bbcodeImage",
	"[imgs" => "bbcodeImageScale",

	"[user" => "bbcodeUser",
	"[thread" => "bbcodeThread",
	"[forum" => "bbcodeForum",

	"[quote" => "bbcodeQuote",
	"[reply" => "bbcodeReply",

	"[spoiler" => "bbcodeSpoiler",
	"[code" => "bbcodeCode",

	"[table" => "bbcodeTable",
	"[tr" => "bbcodeTableRow",
	"[trh" => "bbcodeTableRowHeader",
	"[td" => "bbcodeTableCell",

	'[youtube' => 'bbcodeYoutube',
    '[gist' => 'bbcodeGist',

	//Meme BBCode starts here
	"[instameme" => "bbcodeMeme",
	"[ugotbanned" => "bbcodeBan",
);

//Allow plugins to register their own callbacks (new bbcode tags)
$bucket = "bbcode"; include(__DIR__."/pluginloader.php");

function bbcodeBold($contents, $arg, $parenttag)
{
	return "<strong>$contents</strong>";
}
function bbcodeItalics($contents, $arg, $parenttag)
{
	return "<em>$contents</em>";
}
function bbcodeUnderline($contents, $arg, $parenttag)
{
	return "<u>$contents</u>";
}
function bbcodeStrikethrough($contents, $arg, $parenttag)
{
	return "<del>$contents</del>";
}

function bbcodeURL($contents, $arg, $parenttag)
{
	$dest = htmlentities($contents);
	$title = $contents;

	if($arg)
		$dest = htmlentities($arg);

	return '<a href="'.$dest.'">'.$title.'</a>';
}

function bbcodeURLAuto($match)
{
        $text = $match[0];
	// This is almost like lcfirst() from PHP 5.3.0
	$match[0][0] = strtolower($text[0]);
	if ($match[0][0] === "w") $match[0] = "http://$match[0]";
	return '<a href="'.htmlspecialchars($text).'">'.$match[0].'</a>';
}

function bbcodeImage($contents, $arg, $parenttag)
{
	$dest = $contents;
	$title = "";
	if($arg)
	{
		$title = $contents;
		$dest = $arg;
	}

	return '<img class="imgtag" src="'.htmlspecialchars($dest).'" alt="'.htmlspecialchars($title).'"/>';
}


function bbcodeImageScale($contents, $arg, $parenttag)
{
	$dest = $contents;
	$title = "";
	if($arg)
	{
		$title = $contents;
		$dest = $arg;
	}

	return '<a href="'.htmlspecialchars($dest).'"><img class="imgtag" style="max-width:300px; max-height:300px;" src="'.htmlspecialchars($dest).'" alt="'.htmlspecialchars($title).'"/></a>';
}


function bbcodeUser($contents, $arg, $parenttag)
{
	return UserLinkById((int)$arg);
}

function bbcodeThread($contents, $arg, $parenttag)
{
	global $threadLinkCache, $loguser;
	$id = (int)$arg;
	if(!isset($threadLinkCache[$id]))
	{
		$rThread = Query("select t.id, t.title, t.forum from {threads} t where t.id={0} AND t.forum IN ({1c})", $id, ForumsWithPermission('forum.viewforum'));
		if(NumRows($rThread))
		{
			$thread = Fetch($rThread);
			$threadLinkCache[$id] = makeThreadLink($thread);
		}
		else
			$threadLinkCache[$id] = "&lt;invalid thread ID&gt;";
	}
	return $threadLinkCache[$id];
}

function bbcodeForum($contents, $arg, $parenttag)
{
	global $forumLinkCache, $loguser;
	$id = (int)$arg;
	if(!isset($forumLinkCache[$id]))
	{
		$rForum = Query("select id, title from {forums} where id={0} AND id IN ({1c})", $id, ForumsWithPermission('forum.viewforum'));
		if(NumRows($rForum))
		{
			$forum = Fetch($rForum);
			$forumLinkCache[$id] = actionLinkTag($forum['title'], "forum", $forum['id'], '', HasPermission('forum.viewforum',$forum['id'],true)?$forum['title']:'');
		}
		else
			$forumLinkCache[$id] = "&lt;invalid forum ID&gt;";
	}
	return $forumLinkCache[$id];
}

function bbcodeQuote($contents, $arg, $parenttag)
{
	return bbcodeQuoteGeneric($contents, $arg, __("Posted by"));
}

function bbcodeReply($contents, $arg, $parenttag)
{
	return bbcodeQuoteGeneric($contents, $arg, __("Sent by"));
}

function bbcodeQuoteGeneric($contents, $arg, $text)
{
	if(!$arg)
		return "<div class='quote'><div class='quotecontent'>$contents</div></div>";

	// Possible formats:
	// [quote=blah]
	// [quote="blah blah" id="123"]

	if(preg_match('/"(.*)" id="(.*)"/', $arg, $match))
	{
		$who = htmlspecialchars($match[1]);
		$id = (int) $match[2];
		return "<div class='quote'><div class='quoteheader'><a href=\"".htmlentities(actionLink("post", $id))."\">$text $who</a></div><div class='quotecontent'>$contents</div></div>";
	}
	else
	{
		if ($arg[0] == '"') $arg = substr($arg,1,-1);
		$who = htmlspecialchars($arg);
		return "<div class='quote'><div class='quoteheader'>$text $who</div><div class='quotecontent'>$contents</div></div>";
	}
}

function bbcodeSpoiler($contents, $arg, $parenttag)
{
	if($arg)
		return "<div class=\"spoiler\"><button class=\"spoilerbutton named\">".htmlspecialchars($arg)."</button><div class=\"spoiled hidden\">$contents</div></div>";
	else
		return "<div class=\"spoiler\"><button class=\"spoilerbutton\">Show spoiler</button><div class=\"spoiled hidden\">$contents</div></div>";
}

function bbcodeCode($contents, $arg, $parenttag)
{
	return '<pre><code>'.htmlentities($contents).'</code></pre>';
}

function bbcodeTable($contents, $arg, $parenttag)
{
	return "<table class=\"outline margin\">$contents</table>";
}

$bbcodeCellClass = 0;

function bbcodeTableCell($contents, $arg, $parenttag)
{
	if($parenttag == '[trh')
		return "<th>$contents</th>";
	else
		return "<td>$contents</td>";
}

function bbcodeTableRow($contents, $arg, $parenttag)
{
	global $bbcodeCellClass;
	$bbcodeCellClass++;
	$bbcodeCellClass %= 2;

	return "<tr class=\"cell$bbcodeCellClass\">$contents</tr>";
}

function bbcodeTableRowHeader($contents, $arg, $parenttag)
{
	global $bbcodeCellClass;
	$bbcodeCellClass++;
	$bbcodeCellClass %= 2;

	return "<tr class=\"header0\">$contents</tr>";
}

function getYoutubeIdFromUrl($url)
{
    $pattern =
        '%^# Match any youtube URL
        (?:https?://)?  # Optional scheme. Either http or https
        (?:www\.)?      # Optional www subdomain
        (?:             # Group host alternatives
          youtu\.be/    # Either youtu.be,
        | youtube\.com  # or youtube.com
          (?:           # Group path alternatives
            /embed/     # Either /embed/
          | /v/         # or /v/
          | /watch\?v=  # or /watch\?v=
          )             # End path alternatives.
        )               # End host alternatives.
        ([\w-]{10,12})  # Allow 10-12 for 11 char youtube id.
        $%x'
        ;
    $result = preg_match($pattern, $url, $matches);
    if (false !== $result) {
        return $matches[1];
    }
    return false;
}

function bbcodeYoutube($contents, $arg, $parenttag)
{
	$contents = trim($contents);
	$id = getYoutubeIdFromUrl($contents);
	if($id)
		$contents = $id;

	if(!preg_match("/^[\-0-9_a-zA-Z]+$/", $contents))
		return "[Invalid youtube video ID]";

	return '[youtube]'.$contents.'[/youtube]';
}

//Coding for custom bbcodes start here
function bbcodeGist($contents, $arg) {
    if (!function_exists('curl_init')) {
        return "<a href=\"https://gist.github.com/$contents\">View $contents on GitHub</a>";
    }
    else if (!preg_match("/([0-9_a-zA-Z]+)\/([0-9a-f]+)/", $contents)) {
        return "<pre><code>Invalid Gist</code></pre>";
    }
    else {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://gist.githubusercontent.com/$contents/raw");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $code = curl_exec($ch);
        curl_close($ch);
        return "<pre><code>$code</code></pre>";
    }
}

function bbcodeMeme($contents, $arg, $parenttag)
{
	//Detecting what meme to use from whats inbetween the tag and the close tag
	if ($arg == '1')
		return '<img class="imgtag" style="max-width:300px; max-height:300px;" src="../../img/instameme/instameme1.jpg" alt="Instameme1"/>';
	elseif ($arg == '2')
		return '<img class="imgtag" style="max-width:300px; max-height:300px;" src="../../img/instameme/instameme2.jpg" alt="Instameme2"/>';
	elseif ($arg == '3')
		return '<img class="imgtag" style="max-width:300px; max-height:300px;" src="../../img/instameme/instameme3.jpg" alt="Instameme3"/>';
	elseif ($arg == '4')
		return '<img class="imgtag" style="max-width:300px; max-height:300px;" src="../../img/instameme/instameme4.png" alt="Instameme4"/>';
	elseif ($arg == '5')
		return '<img class="imgtag" style="max-width:300px; max-height:300px;" src="../../img/instameme/instameme5.jpg" alt="Instameme5"/>';
	elseif ($arg == '6')
		return '<img class="imgtag" style="max-width:300px; max-height:300px;" src="../../img/instameme/instameme6.png" alt="Instameme6"/>';
	elseif ($arg == '7')
		return '<img class="imgtag" style="max-width:300px; max-height:300px;" src="../../img/instameme/instameme7.jpg" alt="Instameme7"/>';
	elseif ($contents == '1')
		return '<img class="imgtag" style="max-width:300px; max-height:300px;" src="../../img/instameme/instameme1.jpg" alt="Instameme1"/>';
	elseif ($contents == '2')
		return '<img class="imgtag" style="max-width:300px; max-height:300px;" src="../../img/instameme/instameme2.jpg" alt="Instameme2"/>';
	elseif ($contents == '3')
		return '<img class="imgtag" style="max-width:300px; max-height:300px;" src="../../img/instameme/instameme3.jpg" alt="Instameme3"/>';
	elseif ($contents == '4')
		return '<img class="imgtag" style="max-width:300px; max-height:300px;" src="../../img/instameme/instameme4.png" alt="Instameme4"/>';
	elseif ($contents == '5')
		return '<img class="imgtag" style="max-width:300px; max-height:300px;" src="../../img/instameme/instameme5.jpg" alt="Instameme5"/>';
	elseif ($contents == '6')
		return '<img class="imgtag" style="max-width:300px; max-height:300px;" src="../../img/instameme/instameme6.png" alt="Instameme6"/>';
	elseif ($contents == '7')
		return '<img class="imgtag" style="max-width:300px; max-height:300px;" src="../../img/instameme/instameme7.jpg" alt="Instameme7"/>';
	//If a number was found that is not in the collection
	else
		return '<img class="imgtag" style="max-width:300px; max-height:300px;" src="../../img/instameme/insta404.jpg" alt="Instameme404"/>';
}
function bbcodeBan($contents, $arg, $parenttag)
{
	//Put this here for a 100% true statement to run code under it
	if ('1' == '1')
		return '<img class="imgtag" style="max-width:300px; max-height:300px;" src="../../img/instameme/banhammer.jpg" alt="You got banned"/>';
}
