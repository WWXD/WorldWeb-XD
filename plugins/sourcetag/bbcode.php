<?php

$bbcodeCallbacks["code"] = "bbcodeCodeHighlight";
$bbcodeCallbacks["source"] = "bbcodeCodeHighlight";


function bbcodeCodeHighlight($contents, $arg)
{
	if(!$arg)
	{
		return '<pre><code>'.htmlentities($contents).'</code></pre>';
	}
	else
	{
		return "<pre><code>$code</code></pre>";
	}
}

//I hoped to be able to keep the new parser free from hax :(
//But it's not possible.
//Or is it? ~Dirbaio
function decodeCrapEntities($s)
{
	// parse entities
	$s = preg_replace_callback(
		"/&#(\\d+);/u",
		"_pcreEntityToUtf",
		$s
	);

	return $s;
}

function _pcreEntityToUtf($matches)
{
	$char = intval(is_array($matches) ? $matches[1] : $matches);

	if ($char < 0x80)
	{
		// to prevent insertion of control characters
		if ($char >= 0x20) return htmlspecialchars(chr($char));
		else return "&#$char;";
	}

	/*
	else if ($char < 0x8000)
	{
		return chr(0xc0 | (0x1f & ($char >> 6))) . chr(0x80 | (0x3f & $char));
	}
	else
	{
		return chr(0xe0 | (0x0f & ($char >> 12))) . chr(0x80 | (0x3f & ($char >> 6))). chr(0x80 | (0x3f & $char));
	}*/
}
