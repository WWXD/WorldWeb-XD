<?php
if (!defined('BLARG')) die();

	$settings = [
		"boardname" => [
			"type" => "text",
			"default" => "WorldWeb XD",
			"name" => "Board name",
			'category' => 'Board identity'
		],
		"metaDescription" => [
			"type" => "text",
			"default" => "A WorldWeb XD",
			"name" => "Meta description",
			'category' => 'Board identity'
		],
		"metaTags" => [
			"type" => "text",
			"default" => "WorldWeb, World, Web",
			"name" => "Meta tags",
			'category' => 'Board identity'
		],
		"breadcrumbsMainName" => [
			"type" => "text",
			"default" => "Main",
			"name" => "Text in breadcrumbs' first link",
			'category' => 'Board identity'
		],
		"layout_credits" => [
			"type" => "text",
			"default" => "Site ran by [user=1]",
			"name" => "Custom Credits",
			'category' => 'Board identity'
		],
		"minwords" => [
			"type" => "integer",
			"default" => "5",
			"name" => "Minimum word count"
		],
		'newsForum' => [
			'type' => 'forum',
			'default' => '0',
			'name' => 'Latest News forum'
		],
		'anncForum' => [
			'type' => 'forum',
			'default' => '0',
			'name' => 'Announcements forum'
		],
		"trashForum" => [
			"type" => "forum",
			"default" => "0",
			"name" => "Trash forum"
		],
		"secretTrashForum" => [
			"type" => "forum",
			"default" => "0",
			"name" => "Deleted threads forum"
		],
		"tagsDirection" => [
			"type" => "options",
			"options" => ['Left' => 'Left', 'Right' => 'Right'],
			"default" => 'Right',
			"name" => "Direction of thread tags"
		],
		"alwaysMinipic" => [
			"type" => "boolean",
			"default" => "0",
			"name" => "Show minipics everywhere"
		],
		"showExtraSidebar" => [
			"type" => "boolean",
			"default" => "1",
			"name" => "Show extra info in post sidebar"
		],
		"profilePreviewText" => [
			"type" => "textbbcode",
			"default" => "This is a sample post. You [b]probably[/b] [i]already[/i] [u]know[/u] what this is for.
[quote=Goomba][quote=Mario]Woohoo! [url=http://www.mariowiki.com/Super_Mushroom]That's what I needed![/url][/quote]Oh, nooo! *stomp*[/quote]
Well, what more could you [url=http://en.wikipedia.org]want to know[/url]? Perhaps how to do the classic infinite loop?
[source=c]while(true){
    printf(\"Hello World!
\");
}[/source]",
			"name" => "Post preview text"
		],
		"Syndromes" => [
			"type" => "options",
			"options" => ['0' => 'None', '1' => 'WorldWeb XD', '2' => 'Acmlmboard 2.0', '3' => 'Neritic Net', '4' => 'Vizzed'],
			"default" => '1',
			"name" => "Syndromes"
		],
		"postLayoutType" => [
			"type" => "options",
			"options" => ['0' => 'Signature', '1' => 'Post header + signature', '2' => 'Post header + signature + sidebars'],
			"default" => '2',
			"name" => "Post layout type"
		],
		"postAttach" => [
			"type" => "boolean",
			"default" => "0",
			"name" => "Allow post attachments"
		],
		"customTitleThreshold" => [
			"type" => "integer",
			"default" => "100",
			"name" => "Custom title threshold (posts)"
		],
		"oldThreadThreshold" => [
			"type" => "integer",
			"default" => "3",
			"name" => "Old thread threshold (months)"
		],
		"minwords" => [
			"type" => "integer",
			"default" => "5",
			"name" => "Minimum post word count"
		],
		"floodProtectionInterval" => [
			"type" => "integer",
			"default" => "10",
			"name" => "Minimum time between user posts (seconds)"
		],
		
		
		"dateformat" => [
			"type" => "text",
			"default" => "m-d-y, h:i a",
			"name" => "Default date format",
			'category' => 'Presentation'
		],
		"guestLayouts" => [
			"type" => "boolean",
			"default" => "0",
			"name" => "Show post layouts to guests",
			'category' => 'Presentation'
		],
		"defaultTheme" => [
			"type" => "theme",
			"default" => "blargboard",
			"name" => "Default board theme",
			'category' => 'Presentation'
		],
		"defaultLayout" => [
			"type" => "layout",
			"default" => "bbxd",
			"name" => "Board layout",
			'category' => 'Presentation'
		],
		"showGender" => [
			"type" => "boolean",
			"default" => "1",
			"name" => "Color usernames based on gender",
			'category' => 'Presentation'
		],
		"defaultLanguage" => [
			"type" => "language",
			"default" => "en_US",
			"name" => "Board language",
			'category' => 'Presentation'
		],
		"viewcountInterval" => [
			"type" => "integer",
			"default" => "10000",
			"name" => "Viewcount report interval",
			'category' => 'Functionality'
		],
		"ajax" => [
			"type" => "boolean",
			"default" => "1",
			"name" => "Enable AJAX",
			'category' => 'Functionality'
		],
		"ownerEmail" => [
			"type" => "text",
			"default" => "",
			"name" => "Owner email address",
			"help" => "This email address will be shown to IP-banned users and on other occasions.",
			'category' => 'Functionality'
		],
		"mailResetSender" => [
			"type" => "text",
			"default" => "",
			"name" => "Password Reset email sender",
			"help" => "Email address used to send the pasword reset e-mails. If left blank, the password reset feature is disabled.",
			'category' => 'Functionality'
		],
		"nofollow" => [
			"type" => "boolean",
			"default" => "0",
			"name" => "Add rel=nofollow to all user-posted links",
			'category' => 'Functionality'
		],
		"maintenance" => [
			"type" => "boolean",
			"default" => "0",
			"name" => "Maintenance mode",
			'category' => 'Functionality',
			'rootonly' => 1,
		],


		'PoRATitle' => [
			'type' => 'text',
			'default' => 'Blargbox',
			'name' => 'Info box title',
			'category' => 'Information',
		],
		"PoRAText" => [
			"type" => "textbox",
			"default" => "Welcome to your new WorldWeb XD Website! You can edit the board settings, forum list, this very message, and other stuff from the admin panel.<br/>Enjoy WorldWeb XD!",
			"name" => "Info box text",
			'category' => 'Information',
		],
		"rssTitle" => [
			"type" => "text",
			"default" => "Blargboard RSS",
			"name" => "RSS feed title",
			'category' => 'Information',
		],
		"rssDesc" => [
			"type" => "text",
			"default" => "A news feed for Blargboard",
			"name" => "RSS feed description",
			'category' => 'Information',
		],


		"EmailVerification" => [
			"type" => "boolean",
			"default" => "0",
			"name" => "Email Verification (Verification part not working just yet)",
			'category' => 'Registration settings'
		],
		"math" => [
			"type" => "boolean",
			"default" => "0",
			"name" => "Math question",
			'category' => 'Registration settings'
		],
		"RegWordKey" => [
			"type" => "text",
			"default" => "",
			"name" => "Registration Key",
			"help" => "This is the actual registration key used. Leave blank in order to not use this function.",
			'category' => 'Registration settings'
		],
		"Captcha" => [
			"type" => "boolean",
			"options" => ['0' => 'None', '1' => 'PHPCaptcha', '2' => 'BotDetect Captcha'],
			"default" => "0",
			"name" => "Captcha",
			"help" => "You'll need to download the files seperately... You can either download <a href=\"http://www.phpcaptcha.org/\">PHPCaptcha</a> and extract it into a /securimage/ folder, or you can download <a href=\"https://captcha.com/\">BotDetect Captcha</a> and extract it into the /lib/ folder.",
			'category' => 'Registration settings'
		],
		"AdminVer" => [
			"type" => "boolean",
			"default" => "0",
			"name" => "Admin Verification",
			'category' => 'Registration settings'
		],
		"PassChecker" => [
			"type" => "boolean",
			"default" => "0",
			"name" => "A Password checker.",
			"help" => "Straitly ported from ABXD.",
			'category' => 'Registration settings'
		],
		"DisReg" => [
			"type" => "boolean",
			"default" => "0",
			"name" => "Turn off registration",
			"help" => "Usefull when your site is hit with a spam attack.",
			'category' => 'Registration settings'
		],


		'defaultGroup' => [
			'type' => 'group',
			'default' => 0,
			'name' => 'Group for new users',
			'category' => 'Group settings',
			'rootonly' => 1,
		],
		'rootGroup' => [
			'type' => 'group',
			'default' => 4,
			'name' => 'Group for root users',
			'category' => 'Group settings',
			'rootonly' => 1,
		],
		'bannedGroup' => [
			'type' => 'group',
			'default' => -1,
			'name' => 'Group for banned users',
			'category' => 'Group settings',
			'rootonly' => 1,
		],


		'homepageText' => [
			'type' => 'texthtml',
			'default' => 'Welcome to WorldWeb XD.<br/><br/>Fill this with relevant info.',
			'name' => 'Homepage contents',
			'category' => 'Homepage contents',
		],
		'faqText' => [
			'type' => 'texthtml',
			'default' => 'WorldWeb XD FAQ. Put your rules and stuff here.',
			'name' => 'FAQ contents',
			'category' => 'FAQ contents',
		],
	];
