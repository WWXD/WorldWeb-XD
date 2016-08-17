<?php
//  AcmlmBoard XD - Administration hub page
//  Access: administrators
if (!defined('BLARG')) die();


CheckPermission('admin.viewadminpanel');

$title = __("Administration");

MakeCrumbs(array(actionLink("admin") => __('Admin')));


if (function_exists('curl_init'))
	$protstatus = __('Enabled (using cURL)');
else if (ini_get('allow_url_fopen'))
	$protstatus = __('Enabled (using fopen)');
else
	$protstatus = __('Disabled');


$adminInfo = array();
$adminInfo[__('Proxy protection')] = $protstatus;
$adminInfo[__('Last viewcount milestone')] = $misc['milestone'];


$adminLinks = array();

if ($loguser['root']) 						$adminLinks[] = actionLinkTag(__("Recalculate statistics"), "recalc");
if (HasPermission('admin.manageipbans'))	$adminLinks[] = actionLinkTag(__("Manage IP bans"), "ipbans");
if (HasPermission('admin.editforums'))		$adminLinks[] = actionLinkTag(__("Manage forum list"), "editfora");
if (HasPermission('admin.editsettings'))
{
	$adminLinks[] = actionLinkTag(__("Manage plugins"), "pluginmanager");
	$adminLinks[] = actionLinkTag(__("Edit settings"), "editsettings");
	$adminLinks[] = actionLinkTag(__("Edit home page"), "editsettings", '', 'field=homepageText');
	$adminLinks[] = actionLinkTag(__("Edit FAQ"), "editsettings", '', 'field=faqText');
}
if (HasPermission('admin.editsmilies'))		$adminLinks[] = actionLinkTag(__("Edit smilies"), "editsmilies");
if ($loguser['root'])						$adminLinks[] = actionLinkTag(__("Optimize tables"), "optimize");
if (HasPermission('admin.viewlog'))			$adminLinks[] = actionLinkTag(__("View log"), "log");
if (HasPermission('admin.ipsearch'))		$adminLinks[] = actionLinkTag(__('Rereg radar'), 'reregs');


$bucket = "adminpanel"; include(BOARD_ROOT."lib/pluginloader.php");


RenderTemplate('adminpanel', array('adminInfo' => $adminInfo, 'adminLinks' => $adminLinks));

?>
