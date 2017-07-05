<?php
if (!defined('BLARG')) die();

// ----------------------------------------------------------------------------
// --- General layout functions
// ----------------------------------------------------------------------------

function RenderTemplate($template, $options=null) {
	global $tpl, $mobileLayout, $plugintemplates, $plugins;

	if (array_key_exists($template, $plugintemplates)) {
		$plugin = $plugintemplates[$template];
		$self = $plugins[$plugin];

		$tplroot = BOARD_ROOT.'/plugins/'.$self['dir'].'/layouts/';
	} else
		$tplroot = BOARD_ROOT.'/layouts/';

	if ($mobileLayout) {
		$tplname = $tplroot.'mobile/'.$template.'.tpl';
		if (!file_exists($tplname))
			$tplname = $tplroot.'wwxd/'.$template.'.tpl';
	} else {
		if (Settings::get('defaultLayout') == "")
			$tplname = $tplroot.Settings::get('defaultLayout').$template.'.tpl';
		else
			$tplname = $tplroot.'wwxd/'.$template.'.tpl';
		if (!file_exists($tplname))
			$tplname = $tplroot.'wwxd/'.$template.'.tpl';
	}

	if ($options)
		$tpl->assign($options);

	$tpl->display($tplname);
}

function makeCrumbs($path, $links='') {
	global $layout_crumbs, $layout_actionlinks;

	if(count($path) != 0) {
		$pathPrefix = [actionLink(0) => Settings::get("breadcrumbsMainName")];

		$bucket = "breadcrumbs"; include(__DIR__."/pluginloader.php");

		$path = $pathPrefix + $path;
	}

	$layout_crumbs = $path;
	$layout_actionlinks = $links;
}

function makeBreadcrumbs($path) {
	global $layout_crumbs;
	$path->addStart(new PipeMenuLinkEntry(Settings::get("breadcrumbsMainName"), "board"));
	$path->setClass("breadcrumbs");
	$bucket = "breadcrumbs"; include("lib/pluginloader.php");
	$layout_crumbs = $path;
}