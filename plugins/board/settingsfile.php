<?php
	$settings = array(
		"minwords" => array (
			"type" => "integer",
			"default" => "5",
			"name" => "Minimum word count"
		),
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
			"name" => "Minimum time between user posts (seconds)",
		],
	);
?>