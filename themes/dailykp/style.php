<?php
header("Content-Type: text/css");

$curtime = getdate(time());
$min = $curtime['hours'] * 60 + $curtime['minutes'];

$hue = ($min / 2) % 360;
$hue2 = (($min / 2) + 20) % 360;
$comp = (($min / 2) + 180) % 360;
$sat = 93; //50;

$hs = $hue.", ".$sat."%";
$hs2 = $hue2.", ".$sat."%";
$cp = $comp.", 20%";

$css = "/* AcmlmBoard XD - Daily Kaypea */
@import url('../../css/borders-ab2.css');

body
{
	background: hsl([huesat], 15%) url('background.png');
}

table.outline, table.message, table.post 
{
	border: 1px solid #966;
}

table.outline > tbody > tr > th,
table.message > tbody > tr > th
{
	border-bottom-width: 1px;
}

table.outline > tbody:first-child > tr:first-child > *,
table.message > tbody:first-child > tr:first-child > *,
table.post td.userlink, table.post td.meta
{
	border-top-width: 1px !important;
}

table.outline > tbody > tr > *:first-child,
table.message > tbody > tr > *:first-child,
table.post td.userlink, table.post td.side
{
	border-left-width: 1px !important;
}

.cell0, table.post td.post
{
	background: hsl([huesat2], 13%) url('lines.png');
}

.cell1, button, input[type=submit], table.post, .faq, .errorc, .post_content
{
	background: hsl([huesat2], 18%) url('lines.png');
}

.cell2
{
	background: hsl([huesat2], 25%) url('lines.png');
}

.header0 th
{
	background-color: hsl([huesat], 38%);
	background-image: url('lines.png'), [prefix]linear-gradient(hsl([huesat], 38%), hsl([huesat], 18%) 50%);
	color: #fff;
	text-shadow: 1px 1px 0px #000;
}

.header1 th
{
	background-color: hsl([huesat], 47%);
	background-image: url('lines.png'), [prefix]linear-gradient(hsl([huesat], 47%), hsl([huesat], 28%) 50%);
	color: #fff;
	text-shadow: 1px 1px 0px #000;
}

h3
{
	border-top: 0px none;
	border-bottom-color: hsl([huesat], 48%);
}

#pmNotice
{
	background: hsla([huesat], 48%, 0.75);
}

#pmNotice:hover
{
	background: hsl([huesat], 48%);
}

input[type=text], input[type=password], input[type=file], input[type=email], select, textarea
{
	border: 2px ridge #77D;
	background: #000;
	color: #fff;
}

table, td, tr, th
{
	border-color: hsl([huesat], 10%);
}

.post_about, .post_topbar
{
	background: hsl([huesat], 16%) url('lines.png');
}


div#tabs button
{
	border-top-left-radius: 8px;
	border-top-right-radius: 32px;
	border-bottom-left-radius: 0px;
	border-bottom-right-radius: 0px;
	padding-right: 16px;
	background: hsl([huesat], 30%);
}

div#tabs button.selected
{
	position: static;
	z-index: -100;
	border-bottom: 1px solid hsl([huesat], 20%);
	background: hsl([huesat], 40%);
}

button, input[type=submit]
{
	border: 2px ridge rgb(255,208,64);
	color: rgb(255,208,64);
	font-weight: bold;
}
";

$u = $_SERVER['HTTP_USER_AGENT'];
$webkit = preg_match('/Safari/i', $u) || preg_match('/Chrome/i', $u);
$firefox = preg_match('/Firefox/i', $u);
$opera = preg_match('/Opera/i', $u);
$msie = preg_match('/MSIE/i', $u);
$prefix = "";
if($opera) $prefix = "-o-";
elseif($firefox) $prefix = "-moz-";
elseif($msie) $prefix = "-ms-";
elseif($webkit) $prefix = "-webkit-";

print str_replace("[comp]", $cp, str_replace("[huesat2]", $hs2, str_replace("[huesat]", $hs, str_replace("[prefix]", $prefix, $css))));

?>
