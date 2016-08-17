<?php
if (!defined('BLARG')) die();

	$settings = array(
		"boardname" => array (
			"type" => "text",
			"default" => "Blargboard",
			"name" => "Board name",
			'category' => 'Board identity'
		),
		"metaDescription" => array (
			"type" => "text",
			"default" => "A Blargboard board",
			"name" => "Meta description",
			'category' => 'Board identity'
		),
		"metaTags" => array (
			"type" => "text",
			"default" => "blargboard blarg board",
			"name" => "Meta tags",
			'category' => 'Board identity'
		),
		"breadcrumbsMainName" => array (
			"type" => "text",
			"default" => "Main",
			"name" => "Text in breadcrumbs' first link",
			'category' => 'Board identity'
		),
		"menuMainName" => array (
			"type" => "text",
			"default" => "Main",
			"name" => "Text in menu's first link",
			'category' => 'Board identity'
		),
		
		
		"dateformat" => array (
			"type" => "text",
			"default" => "m-d-y, h:i a",
			"name" => "Default date format",
			'category' => 'Presentation'
		),
		"guestLayouts" => array (
			"type" => "boolean",
			"default" => "0",
			"name" => "Show post layouts to guests",
			'category' => 'Presentation'
		),
		"defaultTheme" => array (
			"type" => "theme",
			"default" => "blargboard",
			"name" => "Default board theme",
			'category' => 'Presentation'
		),
		"showGender" => array (
			"type" => "boolean",
			"default" => "1",
			"name" => "Color usernames based on gender",
			'category' => 'Presentation'
		),
		"defaultLanguage" => array (
			"type" => "language",
			"default" => "en_US",
			"name" => "Board language",
			'category' => 'Presentation'
		),
		"tagsDirection" => array (
			"type" => "options",
			"options" => array('Left' => 'Left', 'Right' => 'Right'),
			"default" => 'Right',
			"name" => "Direction of thread tags",
			'category' => 'Presentation'
		),
		"alwaysMinipic" => array (
			"type" => "boolean",
			"default" => "0",
			"name" => "Show minipics everywhere",
			'category' => 'Presentation'
		),
		"showExtraSidebar" => array (
			"type" => "boolean",
			"default" => "1",
			"name" => "Show extra info in post sidebar",
			'category' => 'Presentation'
		),
		"profilePreviewText" => array (
			"type" => "textbbcode",
			"default" => "This is a sample post. You [b]probably[/b] [i]already[/i] [u]know[/u] what this is for.

[quote=Goomba][quote=Mario]Woohoo! [url=http://www.mariowiki.com/Super_Mushroom]That's what I needed![/url][/quote]Oh, nooo! *stomp*[/quote]

Well, what more could you [url=http://en.wikipedia.org]want to know[/url]? Perhaps how to do the classic infinite loop?
[source=c]while(true){
    printf(\"Hello World!
\");
}[/source]",
			"name" => "Post preview text",
			'category' => 'Presentation'
		),
		
		
		"postLayoutType" => array (
			"type" => "options",
			"options" => array('0' => 'Signature', '1' => 'Post header + signature', '2' => 'Post header + signature + sidebars'),
			"default" => '2',
			"name" => "Post layout type",
			'category' => 'Functionality'
		),
		"postAttach" => array (
			"type" => "boolean",
			"default" => "0",
			"name" => "Allow post attachments",
			'category' => 'Functionality'
		),
		"customTitleThreshold" => array (
			"type" => "integer",
			"default" => "100",
			"name" => "Custom title threshold (posts)",
			'category' => 'Functionality'
		),
		"oldThreadThreshold" => array (
			"type" => "integer",
			"default" => "3",
			"name" => "Old thread threshold (months)",
			'category' => 'Functionality'
		),
		"viewcountInterval" => array (
			"type" => "integer",
			"default" => "10000",
			"name" => "Viewcount report interval",
			'category' => 'Functionality'
		),
		"ajax" => array (
			"type" => "boolean",
			"default" => "1",
			"name" => "Enable AJAX",
			'category' => 'Functionality'
		),
		"ownerEmail" => array (
			"type" => "text",
			"default" => "",
			"name" => "Owner email address",
			"help" => "This email address will be shown to IP-banned users and on other occasions.",
			'category' => 'Functionality'
		),
		"mailResetSender" => array (
			"type" => "text",
			"default" => "",
			"name" => "Password Reset email sender",
			"help" => "Email address used to send the pasword reset e-mails. If left blank, the password reset feature is disabled.",
			'category' => 'Functionality'
		),
		"floodProtectionInterval" => array (
			"type" => "integer",
			"default" => "10",
			"name" => "Minimum time between user posts (seconds)",
			'category' => 'Functionality'
		),
		"nofollow" => array (
			"type" => "boolean",
			"default" => "0",
			"name" => "Add rel=nofollow to all user-posted links",
			'category' => 'Functionality'
		),
		"maintenance" => array (
			"type" => "boolean",
			"default" => "0",
			"name" => "Maintenance mode",
			'category' => 'Functionality',
			'rootonly' => 1,
		),
		
		
		'PoRATitle' => array(
			'type' => 'text',
			'default' => 'Blargbox',
			'name' => 'Info box title',
			'category' => 'Information',
		),
		"PoRAText" => array (
			"type" => "textbox",
			"default" => "Welcome to Blargboard. Edit this.",
			"name" => "Info box text",
			'category' => 'Information',
		),
		"rssTitle" => array (
			"type" => "text",
			"default" => "Blargboard RSS",
			"name" => "RSS feed title",
			'category' => 'Information',
		),
		"rssDesc" => array (
			"type" => "text",
			"default" => "A news feed for Blargboard",
			"name" => "RSS feed description",
			'category' => 'Information',
		),
		
		
		'newsForum' => array(
			'type' => 'forum',
			'default' => '0',
			'name' => 'Latest News forum',
			'category' => 'Forum settings',
		),
		'anncForum' => array(
			'type' => 'forum',
			'default' => '0',
			'name' => 'Announcements forum',
			'category' => 'Forum settings',
		),
		"trashForum" => array (
			"type" => "forum",
			"default" => "0",
			"name" => "Trash forum",
			'category' => 'Forum settings',
		),
		"secretTrashForum" => array (
			"type" => "forum",
			"default" => "0",
			"name" => "Deleted threads forum",
			'category' => 'Forum settings',
		),
		
		
		'defaultGroup' => array (
			'type' => 'group',
			'default' => 0,
			'name' => 'Group for new users',
			'category' => 'Group settings',
			'rootonly' => 1,
		),
		'rootGroup' => array (
			'type' => 'group',
			'default' => 4,
			'name' => 'Group for root users',
			'category' => 'Group settings',
			'rootonly' => 1,
		),
		'bannedGroup' => array (
			'type' => 'group',
			'default' => -1,
			'name' => 'Group for banned users',
			'category' => 'Group settings',
			'rootonly' => 1,
		),
		
		
		'homepageText' => array(
			'type' => 'texthtml',
			'default' => 'Welcome to Blargboard.<br><br>Fill this with relevant info.',
			'name' => 'Homepage contents',
			'category' => 'Homepage contents',
		),
		'faqText' => array(
			'type' => 'texthtml',
			'default' => 'Blargboard FAQ. Put your rules and stuff here.',
			'name' => 'FAQ contents',
			'category' => 'FAQ contents',
		),
	);
?>
