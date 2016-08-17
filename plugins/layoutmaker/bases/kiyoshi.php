<?php

$cssTemplate = ".mainbar[ID]
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

.mainbar[ID] div.top, .mainbar[ID] div.bottom
{
	background: rgba(0,0,0, [OPACITY]);
	margin: [MARGIN][MARGINTYPE];
	padding: [INNERPADDING][MARGINTYPE];
	border: [BORDER];
	border-radius: [RADIUS][RADIUSTYPE];
	color: [COLOR];
	font-family: \"[FONT]\";
	[TEXTFX]
}
.mainbar[ID] div.wrap
{
	background: url(\"[URL]\") top right no-repeat;
}
.mainbar[ID] div.top
{
	margin-right: [SIZE]px;
}
";

$markupTemplateA = "<div class=\"wrap\"><div class=\"top\">";
$markupTemplateB = "</div><div class=\"bottom\">Extra stuff here</div></div>";

$parameters = array
(
	"ID" => array("label"=>"User ID", "type"=>"int"),
	"BACKGROUND" => array("label"=>"Background", "type"=>"background", "default"=>"#004400"),
	"MARGIN" => array("label"=>"Margin", "type"=>"int", "default"=>"15", "pxem"=>"1"),
	"PADDING" => array("label"=>"Padding", "type"=>"int", "default"=>"15", "pxem"=>"1"),
	"INNERPADDING" => array("label"=>"Inner padding", "type"=>"int", "default"=>"15", "pxem"=>"1"),
	"OPACITY" => array("label"=>"Opacity", "type"=>"percentage", "default"=>"50"),
	"BORDER" => array("label"=>"Border", "type"=>"border", "default"=>"1px solid #FFA500"),
	"RADIUS" => array("label"=>"Corner radius", "type"=>"range", "min"=>"0", "max"=>"16", "default"=>"8", "pxem"=>"1"),
	"COLOR" => array("label"=>"Color", "type"=>"color", "default"=>"#FFFFFF"),
	"FONT" => array("label"=>"Font", "type"=>"font", "default"=>"Lucida Sans Unicode"),
	"URL" => array("label"=>"Image URL", "type"=>"string", "default"=>"http://helmet.kafuka.org/cynthbiopic.png"),
	"SIZE" => array("label"=>"Image width", "type"=>"int", "default"=>"256"),

	"TEXTFX" => array("label"=>"Text effect", "type"=>"textfx"),

	"MARGINTYPE" => array("hidden"=>"1"),
	"PADDINGTYPE" => array("hidden"=>"1"),
	"RADIUSTYPE" => array("hidden"=>"1"),

);

?>
