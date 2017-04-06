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
			$tplname = $tplroot.'bbxd/'.$template.'.tpl';
	} else {
		$tplname = $tplroot.Settings::get('defaultLayout').$template.'.tpl';
		if (!file_exists($tplname))
			$tplname = $tplroot.'bbxd/'.$template.'.tpl';
	}

	if ($options)
		$tpl->assign($options);

	$tpl->display($tplname);
}