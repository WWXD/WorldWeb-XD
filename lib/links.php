<?php
if (!defined('BLARG')) die();

$ishttps = ($_SERVER['SERVER_PORT'] == 443);
$serverport = ($_SERVER['SERVER_PORT'] == ($ishttps?443:80)) ? '' : ':'.$_SERVER['SERVER_PORT'];

function urlNamify($urlname)
{
	$urlname = strtolower($urlname);
	$urlname = str_replace("&", "and", $urlname);
	$urlname = preg_replace("/[^a-zA-Z0-9]/", "-", $urlname);
	$urlname = preg_replace("/-+/", "-", $urlname);
	$urlname = preg_replace("/^-/", "", $urlname);
	$urlname = preg_replace("/-$/", "", $urlname);
	return $urlname;
}

function actionLink($action, $id="", $args="", $urlname="")
{
	$boardroot = URL_ROOT;
	if($boardroot == "")
		$boardroot = "./";

	// calling plugins at EVERY link?! way to waste performances
	//$bucket = "linkMangler"; include(__DIR__."/pluginloader.php");
	
	// rewritten links
	/*if ($action == MAIN_PAGE) $action = '';
	else $action .= '/';
	
	if ($id)
	{
		if ($urlname) $id .= '-'.urlNamify($urlname);
		$id .= '/';
	}
	else $id = '';
	
	return $boardroot.$action.$id.($args ? '?'.$args : '');*/

	// non-rewritten links
	$res = "";

	if($action != MAIN_PAGE)
		$res .= "&page=$action";

	if($id != "")
		$res .= "&id=".urlencode($id);
	if($args)
		$res .= "&$args";

	if($res == "")
		return $boardroot;
	else
		return $boardroot."?".substr($res, 1);
}


function actionLinkTag($text, $action, $id='', $args="", $urlname="")
{
	return '<a href="'.htmlentities(actionLink($action, $id, $args, $urlname)).'">'.$text.'</a>';
}
function actionLinkTagItem($text, $action, $id='', $args="", $urlname="")
{
	return '<li><a href="'.htmlentities(actionLink($action, $id, $args, $urlname)).'">'.$text.'</a></li>';
}

function actionLinkTagConfirm($text, $prompt, $action, $id='', $args="")
{
	return '<a onclick="return confirm(\''.$prompt.'\'); " href="'.htmlentities(actionLink($action, $id, $args)).'">'.$text.'</a>';
}
function actionLinkTagItemConfirm($text, $prompt, $action, $id='', $args="")
{
	return '<li><a onclick="return confirm(\''.$prompt.'\'); " href="'.htmlentities(actionLink($action, $id, $args)).'">'.$text.'</a></li>';
}

function getForm($action, $id='')
{
	$ret = '<form method="GET"><input type="hidden" name="page" value="'.$action.'">';
	
	if ($id != '')
		$ret .= '<input type="hidden" name="id" value="'.htmlspecialchars($id).'">';
	
	return $ret;
}

function resourceLink($what)
{
	return URL_ROOT.$what;
}

function themeResourceLink($what)
{
	global $theme;
	return URL_ROOT."themes/$theme/$what";
}

function getMinipicTag($user)
{
	if (!$user['minipic']) return '';
	$pic = str_replace('$root/', DATA_URL, $user['minipic']);
	$minipic = "<img src=\"".htmlspecialchars($pic)."\" alt=\"\" class=\"minipic\" />&nbsp;";
	return $minipic;
}

function prettyRainbow($s)
{
	$r = mt_rand(0,359);
	$s = str_replace('&nbsp;', ' ', $s);
	$s = html_entity_decode($s);
	$len = strlen($s);
	$out = '';
	for ($i = 0; $i < $len;)
	{
		$c = $s[$i++];
		
		if ($c == ' ')
		{
			$out .= '&nbsp;';
			continue;
		}
		
		$ord = ord($c);
		if ($ord & 0x80)		// UTF8 shiz
		{
			if ($ord & 0x40)
			{
				$c .= $s[$i++];
				if ($ord & 0x20)
				{
					$c .= $s[$i++];
					if ($ord & 0x10)
					{
						$c .= $s[$i++];
						if ($ord & 0x08)
						{
							$c .= $s[$i++];
							if ($ord & 0x04)
							{
								$c .= $s[$i++];
							}
						}
					}
				}
			}
		}
		else 
		{
			if ($c == '<') $c = '&lt;';
			else if ($c == '>') $c = '&gt;';
		}
		
		$out .= '<span style="color:hsl('.$r.',100%,80.4%);">'.$c.'</span>';
		$r += 31;
		$r %= 360;
	}
	return $out;
}

$poptart = mt_rand(0,359);
$dorainbow = -1;

function userLink($user, $showMinipic = false, $customID = false)
{
	global $usergroups;
	global $poptart, $dorainbow, $newToday;
	global $luckybastards;
	
	if ($dorainbow == -1)
	{
		$dorainbow = false;
		
		if ($newToday >= 600)
			$dorainbow = true;
	}

	$fgroup = $usergroups[$user['primarygroup']];
	$fsex = $user['sex'];
	$fname = ($user['displayname'] ? $user['displayname'] : $user['name']);
	$fname = htmlspecialchars($fname);
	$fname = str_replace(" ", "&nbsp;", $fname);
	
	$isbanned = $fgroup['id'] == Settings::get('bannedGroup');

	$minipic = "";
	if($showMinipic || Settings::get("alwaysMinipic"))
		$minipic = getMinipicTag($user);
	
	if(!Settings::get("showGender"))
		$fsex = 2;
	//else if ($fsex != 2)
	//	$fsex = $fsex ? 0:1; // switch male/female for the lulz
	
	if ($fsex == 0) $scolor = 'color_male';
	else if ($fsex == 1) $scolor = 'color_female';
	else $scolor = 'color_unspec';
	
	$classing = ' style="color: '.htmlspecialchars($fgroup[$scolor]).';"';

	$bucket = "userLink"; include(__DIR__."/pluginloader.php");
	
	if (!$isbanned && $luckybastards && in_array($user['id'], $luckybastards))
	{
		$classing = ' style="text-shadow:0px 0px 4px;"';
		$fname = prettyRainbow($fname);
	}
	else if ($dorainbow)
	{
		if (!$isbanned)
			$classing = ' style="color:hsl('.$poptart.',100%,80.4%);"';
		$poptart += 31;
		$poptart %= 360;
	}
	
	$fname = $minipic.$fname;
	
	if ($customID)
		$classing .= " id=\"$customID\"";
	
	$title = htmlspecialchars($user['name']) . ' ('.$user["id"].') ['.htmlspecialchars($fgroup['title']).']';
	if ($user['id'] == 0) return "<strong$classing class=\"userlink fake\">$fname</strong>";
	return actionLinkTag("<span$classing class=\"userlink\" title=\"$title\">$fname</span>", "profile", $user["id"], "", $user["name"]);
}

function userLinkById($id)
{
	global $userlinkCache;

	if(!isset($userlinkCache[$id]))
	{
		$rUser = Query("SELECT u.(_userfields) FROM {users} u WHERE u.id={0}", $id);
		if(NumRows($rUser))
			$userlinkCache[$id] = getDataPrefix(Fetch($rUser), "u_");
		else
			$userlinkCache[$id] = array('id' => 0, 'name' => "Unknown User", 'sex' => 0, 'primarygroup' => -1);
	}
	return UserLink($userlinkCache[$id]);
}

function makeThreadLink($thread)
{
	$tags = ParseThreadTags($thread['title']);

	$link = actionLinkTag($tags[0], 'thread', $thread['id'], '', HasPermission('forum.viewforum',$thread['forum'],true)?$tags[0]:'');
	$tags = $tags[1];

	if (Settings::get("tagsDirection") === 'Left')
		return $tags." ".$link;
	else
		return $link." ".$tags;

}
function makeFromUrl($url, $from)
{
	if($from == 0)
	{
		$url = preg_replace('@(?:&amp;|&|\?)\w+=$@', '', $url);
		return $url;
	}
	else return $url.$from;
}

function pageLinks($url, $epp, $from, $total)
{
	if ($total <= $epp) return '';
	
	$url = htmlspecialchars($url);

	if($from < 0) $from = 0;
	if($from > $total-1) $from = $total-1;
	$from -= $from % $epp;

	$numPages = (int)ceil($total / $epp);
	$page = (int)ceil($from / $epp) + 1;

	$first = ($from > 0) ? "<a class=\"pagelink firstpage\" href=\"".makeFromUrl($url, 0)."\">&#x00AB;</a> " : "";
	$prev = $from - $epp;
	if($prev < 0) $prev = 0;
	$prev = ($from > 0) ? "<a class=\"pagelink prevpage\"  href=\"".makeFromUrl($url, $prev)."\">&#x2039;</a> " : "";
	$next = $from + $epp;
	$last = ($numPages * $epp) - $epp;
	if($next > $last) $next = $last;
	$next = ($from < $total - $epp) ? " <a class=\"pagelink nextpage\"  href=\"".makeFromUrl($url, $next)."\">&#x203A;</a>" : "";
	$last = ($from < $total - $epp) ? " <a class=\"pagelink lastpage\"  href=\"".makeFromUrl($url, $last)."\">&#x00BB;</a>" : "";

	$pageLinks = array();
	for($p = $page - 5; $p < $page + 5; $p++)
	{
		if($p < 1 || $p > $numPages)
			continue;
		if($p == $page || ($from == 0 && $p == 1))
			$pageLinks[] = "<span class=\"pagelink curpage\">$p</span>";
		else
			$pageLinks[] = "<a class=\"pagelink\"  href=\"".makeFromUrl($url, (($p-1) * $epp))."\">".$p."</a>";
	}

	return $first.$prev.join($pageLinks, "").$next.$last;
}

function pageLinksInverted($url, $epp, $from, $total)
{
	if ($total <= $epp) return '';
	
	$url = htmlspecialchars($url);

	if($from < 0) $from = 0;
	if($from > $total-1) $from = $total-1;
	$from -= $from % $epp;

	$numPages = (int)ceil($total / $epp);
	$page = (int)ceil($from / $epp) + 1;

	$first = ($from > 0) ? "<a class=\"pagelink\" href=\"".makeFromUrl($url, 0)."\">&#x00BB;</a> " : "";
	$prev = $from - $epp;
	if($prev < 0) $prev = 0;
	$prev = ($from > 0) ? "<a class=\"pagelink\"  href=\"".makeFromUrl($url, $prev)."\">&#x203A;</a> " : "";
	$next = $from + $epp;
	$last = ($numPages * $epp) - $epp;
	if($next > $last) $next = $last;
	$next = ($from < $total - $epp) ? " <a class=\"pagelink\"  href=\"".makeFromUrl($url, $next)."\">&#x2039;</a>" : "";
	$last = ($from < $total - $epp) ? " <a class=\"pagelink\"  href=\"".makeFromUrl($url, $last)."\">&#x00AB;</a>" : "";

	$pageLinks = array();
	for($p = $page + 5; $p >= $page - 5; $p--)
	{
		if($p < 1 || $p > $numPages)
			continue;
		if($p == $page || ($from == 0 && $p == 1))
			$pageLinks[] = "<span class=\"pagelink\">".($numPages+1-$p)."</span>";
		else
			$pageLinks[] = "<a class=\"pagelink\"  href=\"".makeFromUrl($url, (($p-1) * $epp))."\">".($numPages+1-$p)."</a>";
	}

	return $last.$next.join($pageLinks, "").$prev.$first;
}


function absoluteActionLink($action, $id=0, $args="")
{
	global $serverport;
    return ($https?"https":"http") . "://" . $_SERVER['SERVER_NAME'].$serverport.dirname($_SERVER['PHP_SELF']).substr(actionLink($action, $id, $args), 1);
}

function getRequestedURL()
{
    return $_SERVER['REQUEST_URI'];
}


function getServerDomainNoSlash($https = false)
{
	global $serverport;
	return ($https?"https":"http") . "://" . $_SERVER['SERVER_NAME'].$serverport;
}

function getServerURL($https = false)
{
    return getServerURLNoSlash($https)."/";
}

function getServerURLNoSlash($https = false)
{
    global $serverport;
    return ($https?"https":"http") . "://" . $_SERVER['SERVER_NAME'].$serverport . substr(URL_ROOT, 0, strlen(URL_ROOT)-1);
}

function getFullRequestedURL($https = false)
{
    return getServerURL($https) . $_SERVER['REQUEST_URI'];
}

function getFullURL()
{
	return getFullRequestedURL();
}

// ----------------------------------------------------------------------------
// --- Smarty interface
// ----------------------------------------------------------------------------

function smarty_function_actionLink($params, $template)
{
	return htmlspecialchars(actionLink($params['page'], ($params['id']?:''), $params['args'], $params['urlname']));
}

function smarty_function_resourceLink($params, $template)
{
	return htmlspecialchars(resourceLink($params['url']));
}

?>
