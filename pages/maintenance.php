<!doctype html>
<html lang="en">
<head>
	<title><?php print $layout_title; ?></title>

	<meta http-equiv="Content-Type" content="text/html; CHARSET=utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=10">
	<meta name="description" content="<?php print $metaStuff['description']; ?>">
	<meta name="keywords" content="<?php print $metaStuff['tags']; ?>">

	<link rel="shortcut icon" type="image/x-icon" href="<?php print $favicon;?>">
	<link rel="stylesheet" type="text/css" href="<?php print resourceLink("css/common.css");?>">
	<link rel="stylesheet" type="text/css" id="theme_css" href="/themes/blargboard/style.css">
	<link rel="stylesheet" type="text/css" href="<?php print resourceLink('css/font-awesome.min.css'); ?>">
    <link rel="stylesheet" href="https://opensource.keycdn.com/fontawesome/4.6.3/font-awesome.min.css" integrity="sha384-Wrgq82RsEean5tP3NK3zWAemiNEXofJsTwTyHmNb/iL3dP/sZJ4+7sOld1uqYJtE" crossorigin="anonymous">
    <link rel="stylesheet" href="<?php print resourceLink('css/w3.css'); ?>">

	<script type="text/javascript" src="<?php print resourceLink("js/jquery.js");?>"></script>
	<script type="text/javascript" src="<?php print resourceLink("js/tricks.js");?>"></script>
	<script type="text/javascript" src="<?php print resourceLink("js/jquery.tablednd_0_5.js");?>"></script>
	<script type="text/javascript" src="<?php print resourceLink("js/jquery.scrollTo-1.4.2-min.js");?>"></script>
	<script type="text/javascript" src="<?php print resourceLink("js/jscolor/jscolor.js");?>"></script>
	<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.2.0/highlight.min.js"></script>
	<script>hljs.initHighlightingOnLoad();</script>
	<script type="text/javascript">boardroot = <?php print json_encode(URL_ROOT); ?>;</script>
	<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

	<?php if ($mobileLayout) { ?>
	<meta name="viewport" content="user-scalable=yes, initial-scale=1.0, width=device-width">
	<script type="text/javascript" src="<?php echo resourceLink('js/mobile.js'); ?>"></script>
	<?php if ($oldAndroid) { ?>
	<style type="text/css">
	#mobile-sidebar { height: auto!important; max-height: none!important; }
	#realbody { max-height: none!important; max-width: none!important; overflow: scroll!important; }
	</style>
	<?php } ?>

	<?php } ?>
</head>
<body style="width:100%; font-size: <?php echo $loguser['fontsize']; ?>%;">
<table id="main" class="layout-table">
<tr>
<td id="main-header" colspan="3">
	<table id="header" class="outline">
		<tr>
			<td class="cell0 center" colspan="3">
				<table class="layout-table">
				<tr>
				<td>
					<a href="/"><img id="theme_banner" src="/img/logo.png" alt="Blargboard" title="Blargboard"></a>
				</td>
				</tr>
				</table>
			</td>		</tr>
		<tr class="header1"><th id="header-sep" colspan="3"></th></tr>
	</table>
</td>
</tr>
<tr>

<td id="main-page">
	<table id="page-container" class="layout-table">
	<tr><td class="contents-container">
		<div id="page_contents">	<table class="outline margin center" style="width: 60%; overflow: auto; margin: auto; margin-top: 40px; margin-bottom: 40px;">
<tr><td class="cell0" style="padding:30px">
	The board is in maintenance mode, please try again later. Our apologies for the inconvenience.
</td></tr></table>	</div>
	</td></tr>
	</table>
</td>
</tr>
</table>

</body>
</html>