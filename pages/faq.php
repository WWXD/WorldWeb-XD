<?php
//  AcmlmBoard XD - Frequently Asked Questions page
//  Access: all
if (!defined('BLARG')) die();

$title = __("FAQ");
$links = array();
if(HasPermission('admin.editsettings'))
	$links[] = actionLinkTag(__("Edit the FAQ"), "editsettings", '', 'field=faqText');

MakeCrumbs(array(actionLink("faq") => __("FAQ")), $links);

makeThemeArrays();

$admin = Fetch(Query("select u.(_userfields) from {users} u where u.primarygroup={0}", Settings::get('rootGroup')));
$admin = userLink(getDataPrefix($admin, 'u_'));

$sexes = array(0=>__("Male"),1=>__("Female"),2=>__("N/A"));
$scolors = array(0 => 'color_male', 1 => 'color_female', 2 => 'color_unspec');

$gcolors = array();
$g = Query("SELECT title, color_male, color_female, color_unspec FROM {usergroups} WHERE type=0 ORDER BY rank");
while ($group = Fetch($g))
	$gcolors[] = $group;

$headers = "";
$colors = "";
foreach($sexes as $ss)
	$headers .= format(
"
	<th>
		{0}
	</th>
", $ss);
foreach($gcolors as $g)
{
	$cellClass = ($cellClass+1) % 2;
	$items = "";
	foreach($sexes as $sn => $ss)
		$items .= format(
"
	<td class=\"center\" style=\"padding:2px!important;\">
		<a href=\"javascript:void()\"><span style=\"color: {0};\">
			{1}
		</span></a>
	</td>
", htmlspecialchars($g[$scolors[$sn]]), htmlspecialchars($g['title']));
	$colors .= format(
"
<tr class=\"cell{0}\">
	{1}
</tr>
", $cellClass, $items);
}
$colortable = format("
<table class=\"width50 outline\" style=\"margin-left: auto; margin-right: auto;\">
	<tr class=\"header1\">
		{0}
	</tr>
	{1}
</table>
", $headers, $colors);

//implode(", ", $themefiles)
$themelist = array();
foreach ($themefiles as $i=>$t)
	$themelist[$t] = $themes[$i];
ksort($themelist);

$finaltlist = '
<table class="width75 outline" style="margin-left: auto; margin-right: auto;">
	<tbody>
		<tr class="header1"><th colspan="6" style="cursor:pointer;" onclick="$(\'#themelist\').toggle();">Themes (click to expand)</th></tr>
	</tbody>
	<tbody id="themelist" style="display:none;">
		<tr class="header0">
			<th style="width:16.67%;">$theme</th><th style="width:16.67%;">Name</th>
			<th style="width:16.67%;">$theme</th><th style="width:16.67%;">Name</th>
			<th style="width:16.67%;">$theme</th><th style="width:16.67%;">Name</th>
		</tr>';
	
$i = 0;
foreach ($themelist as $tid=>$tname)
{
	if (($i % 3) == 0)
		$finaltlist .= '
		<tr class="cell0">';
	
	$finaltlist .= '
			<td class="center"><code>'.htmlspecialchars($tid).'</code></td>
			<td class="cell1 center">'.htmlspecialchars($tname).'</td>';
	
	if (($i % 3) == 2)
		$finaltlist .= '
		</tr>';
	
	$i++;
}

if (($i % 3) != 0)
	$finaltlist .= '
			<td colspan="'.((3-($i%3))*2).'">&nbsp;</td>
		</tr>';

$finaltlist .= '
	</tbody>
</table>';

$faq = Settings::get("faqText");

$faq = str_replace("<colortable />", $colortable, $faq);
if("" == Settings::get("registrationWord"))
	$faq = preg_replace("'<iftheword>(.*)</iftheword>'s", "", $faq);
else
	$faq = str_replace("<theword />", Settings::get("registrationWord"), $faq);

$code1 = '<link rel="stylesheet" type="text/css" href="http://.../MyLayout_$theme.css">';
$code2 = '<link rel="stylesheet" type="text/css" href="http://.../MyLayout_'.$theme.'.css">';
$faq = str_replace("<themeexample1 />", DoGeshi($code1), $faq);
$faq = str_replace("<themeexample2 />", DoGeshi($code2), $faq);
$faq = str_replace("<themelist />", $finaltlist, $faq);
$faq = str_replace("<admin />", $admin, $faq);

echo $faq;

function DoGeshi($code)
{
	return "<code>".htmlspecialchars($code)."</code>";
}

?>
