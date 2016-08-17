<?php

$cssTemplate = "
.mainbar[ID]
{
	background: transparent !important;
	border-top: [BORDER]!important;
	border-left: [BORDER]!important;
}

.topbar[ID]_1, .topbar[ID]_2, .sidebar[ID]
{
	background: rgba(0,0,0, [OPACITY])!important;
	color: [COLOR]!important;
}

.table[ID]
{
	background: [BACKGROUND] !important;
}

.mainbar[ID] div.main
{
	margin: [MARGIN][MARGINTYPE];
	padding: [PADDING][PADDINGTYPE];
	background: rgba(0,0,0, [OPACITY]);
	border: [BORDER];
	border-radius: [RADIUS][RADIUSTYPE];
	box-shadow: [BOXSHADOW];
	color: [COLOR];
	font-family: [FONT];
	[TEXTFX]
}
";

$markupTemplateA = "<table><tr><td><div class=\"main\">";
$markupTemplateB = "</div></td><td><img src=\"[URL]\" alt=\"\" /></td></tr></table>";

$parameters = array
(
	"ID" => array("label"=>"User ID", "type"=>"int"),
	"BACKGROUND" => array("label"=>"Background", "type"=>"background", "default"=>"#000044"),
	"MARGIN" => array("label"=>"Margin", "type"=>"int", "default"=>"15", "pxem"=>"1"),
	"PADDING" => array("label"=>"Padding", "type"=>"int", "default"=>"15", "pxem"=>"1"),
	"OPACITY" => array("label"=>"Opacity", "type"=>"percentage", "default"=>"50"),
	"BORDER" => array("label"=>"Border", "type"=>"border", "default"=>"1px solid #5555FF"),
	"RADIUS" => array("label"=>"Corner radius", "type"=>"range", "min"=>"0", "max"=>"16", "default"=>"8", "pxem"=>"1"),
	"COLOR" => array("label"=>"Color", "type"=>"color", "default"=>"#FFFFFF"),
	"FONT" => array("label"=>"Font", "type"=>"string", "default"=>"Lucida Sans Unicode"),
	"URL" => array("label"=>"Image URL", "type"=>"string", "default"=>"http://helmet.kafuka.org/cynthbiopic.png"),

	"TEXTFX" => array("label"=>"Text effect", "type"=>"textfx"),
	"BOXSHADOW" => array("label"=>"Shadow", "type"=>"boxshadow"),

	"MARGINTYPE" => array("hidden"=>"1"),
	"PADDINGTYPE" => array("hidden"=>"1"),
	"RADIUSTYPE" => array("hidden"=>"1"),
);

?>
