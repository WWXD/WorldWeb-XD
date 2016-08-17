<?php

$cssTemplate = "

.topbar[ID]_1
{
    border-radius:0px!important;
    border-color: [BORDERCOLOR]!important;
    border-width: 1px 0px 0px 1px!important;
    text-align: center;
    background:black!important;
    color:[SIDEBARCOLOR]!important;
    text-shadow: 0px 0px 3px [SIDEBARGLOWCOLOR];
}
.topbar[ID]_1 span:hover
{
    color:#FFFFFF!important;
}
.topbar[ID]_2
{
    border-radius:0px!important;
    background:black!important;
    color:[SIDEBARCOLOR]!important;
    text-shadow: 0px 0px 3px [SIDEBARGLOWCOLOR];
    border-color: [BORDERCOLOR]!important;
    border-width: 1px 1px 0px 0px!important;
}


.topbar[ID]_2 a:link
{
    color:#ffd200!important;
    text-shadow: 0px 0px 1px #ffd200!important;
}
.topbar[ID]_2 a:visited
{
    color:#ffd200!important;
    text-shadow: 0px 0px 1px #ffd200!important;
}
.topbar[ID]_2 a:hover
{
    color:#ffffff!important;
    text-shadow: 1px 1px 6px #ffac69!important;
}
.sidebar[ID]
{
    border-radius:0px!important;
    background:black!important;
    text-align: center;

    color:[SIDEBARCOLOR]!important;
    text-shadow: 0px 0px 3px [SIDEBARGLOWCOLOR];
    border-color: [BORDERCOLOR]!important;
    border-width: 0px 0px 1px 1px!important;
}

.mainbar[ID]
{
    border-radius:0px!important;
    background:black!important;
    color:[COLOR]!important;
    text-shadow: 0px 0px 3px [GLOWCOLOR];
    border-color: [BORDERCOLOR]!important;
    border-width: 0px 1px 1px 0px!important;
    padding-right:150px;
    padding-bottom:10px;
}


.mainbar[ID] div.quote
{
    background:rgba([QUOTECOLOR_RGB], 0.2)!important;
    border-color:rgba([QUOTECOLOR_RGB], 0.7)!important;
    border:1px solid!important;
    color:[QUOTECOLOR]!important;
    text-shadow: 0px 0px 4px [QUOTECOLOR]!important;
}
.mainbar[ID] div.quotecontent
{
    border:0px!important;
}

.mainbar[ID] a:link
{
    color:[LINKCOLOR]!important;
    text-shadow: 0px 0px 1px #ffd200!important;
}
.mainbar[ID] a:visited
{
    color:[LINKCOLOR]!important;
    text-shadow: 0px 0px 1px #ffd200!important;
}
.mainbar[ID] a:hover
{
    color:[LINKHOVERCOLOR]!important;
    text-shadow: 1px 1px 6px #ffac69!important;
}
.mainbar[ID] div.spoiler
{
    text-align:center!important;
    background:rgba([QUOTECOLOR_RGB], 0.2)!important;
    border-color:rgba([QUOTECOLOR_RGB], 0.7)!important;
    border:1px solid!important;
    color:[QUOTECOLOR]!important;
    text-shadow: 0px 0px 4px [GLOWCOLOR]!important;
}

.mainbar[ID] div.geshi
{
    background:rgba([QUOTECOLOR_RGB], 0.2)!important;
    background-image:none;
    border-color:rgba([QUOTECOLOR_RGB], 0.7)!important;
    border:1px solid!important;
    color:[QUOTECOLOR]!important;
    text-shadow: 0px 0px 0px !important;
    padding:6px;
}

.mainbar[ID] div.spoiler button
{
    width:100% !important;
    background:rgba(0, 0, 0, 0.4)!important;
    border-color:rgba([QUOTECOLOR_RGB], 0.7)!important;
    border:1px solid!important;
    color:[QUOTECOLOR]!important;
    text-shadow: 0px 0px 4px [QUOTECOLOR]!important;
    padding:3px!important;
}

.mainbar[ID] div.spoiled
{
    background:transparent!important;
    padding-top:3px!important;
    padding-left:6px!important;
    padding-right:6px!important;
    padding-bottom:6px!important;
    text-align:left!important;
    color:[QUOTECOLOR]!important;
    text-shadow: 0px 0px 4px [QUOTECOLOR]!important;
}

.mainbar[ID] img.imgtag
{
    border:1px solid!important;
    border-color: [BORDERCOLOR]!important;
    box-shadow: 0px 0px 15px [GLOWCOLOR]!important;
    margin:20px;
    vertical-align:middle;
}
.mainbar[ID] img.smiley
{
    box-shadow: 0px 0px 12px [GLOWCOLOR]!important;
    border-radius:8px;
    vertical-align:middle;
}

";

$markupTemplateA = "";
$markupTemplateB = "";

$parameters = array
(
	"ID" => array("label"=>"User ID", "type"=>"int"),
	"COLOR" => array("label"=>"Text Color", "type"=>"color", "default"=>"#FF2200"),
	"GLOWCOLOR" => array("label"=>"Glow Color", "type"=>"color", "default"=>"#FF4D00"),
	"SIDEBARCOLOR" => array("label"=>"Sidebar Text Color", "type"=>"color", "default"=>"#2E8FFF"),
	"SIDEBARGLOWCOLOR" => array("label"=>"Sidebar Glow Color", "type"=>"color", "default"=>"#0810FF"),
	"QUOTECOLOR" => array("label"=>"Quote Color", "type"=>"color", "default"=>"#32CF47"),
	"LINKCOLOR" => array("label"=>"Link Color", "type"=>"color", "default"=>"#FFC000"),
	"LINKHOVERCOLOR" => array("label"=>"Hover Link Color", "type"=>"color", "default"=>"#FFFFFF"),
	"BORDERCOLOR" => array("label"=>"Border Color", "type"=>"color", "default"=>"#ffd200"),
);

?>
