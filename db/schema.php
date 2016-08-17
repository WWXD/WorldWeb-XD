<?php
if (!defined('BLARG')) die();

// TODO
// * standardize the database format so it doesn't depend on MySQL types
// * store indexes under a better form

$utf8ci = ' CHARACTER SET utf8 COLLATE utf8_general_ci';
$hugeInt = "bigint(20) NOT NULL DEFAULT '0'";
$genericInt = "int(11) NOT NULL DEFAULT '0'";
$smallerInt = "int(8) NOT NULL DEFAULT '0'";
$bool = "tinyint(1) NOT NULL DEFAULT '0'";
$notNull = " NOT NULL DEFAULT ''";
$text = "text DEFAULT ''"; //NOT NULL breaks in certain versions/settings.
$postText = "mediumtext$utf8ci DEFAULT ''";
$var128 = "varchar(128)".$notNull;
$var256 = "varchar(256)".$notNull;
$var1024 = "varchar(1024)".$notNull;
$AI = "int(11) NOT NULL AUTO_INCREMENT";
$keyID = "primary key (`id`)";

$tables = array
(
	"badges" => array
	(
		"fields" => array
		(
			"owner" => $genericInt,
			"name" => $var256,
			"color" => $smallerInt,
		),
		"special" => "unique key `steenkinbadger` (`owner`,`name`)"
	),
	"settings" => array
	(
		"fields" => array
		(
			"plugin" => $var128,
			"name" => $var128,
			"value" => $text,
		),
		"special" => "unique key `mainkey` (`plugin`,`name`)"
	),
	
	//Weird column names: An entry means that "blockee" has blocked the layout of "user"
	"blockedlayouts" => array
	(
		"fields" => array
		(
			"user" => $genericInt,
			"blockee" => $genericInt,
		),
		"special" => "key `mainkey` (`blockee`, `user`)"
	),
	"categories" => array
	(
		"fields" => array
		(
			"id" => $AI,
			"name" => $var256,
			"corder" => $smallerInt,
			"board" => "varchar(16)".$notNull,
		),
		"special" => $keyID
	),
	"enabledplugins" => array
	(
		"fields" => array
		(
			"plugin" => $var256,
		),
		"special" => "unique key `plugin` (`plugin`)"
	),
	"favorites" => array
	(
		"fields" => array
		(
			"user" => $genericInt,
			"thread" => $genericInt,			
		),
		"special" => "primary key (`user`, `thread`)"
	),
	"forums" => array
	(
		"fields" => array
		(
			"id" => $AI,
			"title" => $var256,
			"description" => $text,
			"catid" => $smallerInt,
			"numthreads" => $genericInt,
			"numposts" => $genericInt,
			"lastpostdate" => $genericInt,
			"lastpostuser" => $genericInt,
			"lastpostid" => $genericInt,
			"hidden" => $bool,
			"forder" => $smallerInt,
			"board" => "varchar(16)".$notNull,
			"l" => $genericInt,
			"r" => $genericInt,
			"redirect" => $var256,
			"offtopic" => $bool,
		),
		"special" => $keyID.", key `catid` (`catid`), key `l` (`l`), key `r` (`r`)"
	),
	"guests" => array
	(
		"fields" => array
		(
			"id" => $AI,
			"ip" => "varchar(45)".$notNull,
			"date" => $genericInt,
			"lasturl" => "varchar(100)".$notNull,
			"lastforum" => $genericInt,
			"useragent" => "varchar(1024)".$notNull,			
			"bot" => $bool,
		),
		"special" => $keyID.", key `ip` (`ip`), key `bot` (`bot`)"
	),
	"ignoredforums" => array
	(
		"fields" => array
		(
			"uid" => $genericInt,
			"fid" => $genericInt,			
		),
		"special" => "key `mainkey` (`uid`, `fid`)"
	),
	"ip2c" => array
	(
		"fields" => array
		(
			"ip_from" => "bigint(12) NOT NULL DEFAULT '0'",
			"ip_to" => "bigint(12) NOT NULL DEFAULT '0'",
			"cc" => "varchar(2) DEFAULT ''",			
		),
		"special" => "key `ip_from` (`ip_from`)"
	),
	"ipbans" => array
	(
		"fields" => array
		(
			"ip" => "varchar(45)".$notNull,
			"reason" => "varchar(100)".$notNull,			
			"date" => $genericInt,			
			"whitelisted" => $bool,
		),
		"special" => "unique key `ip` (`ip`), key `date` (`date`)"
	),
	"misc" => array
	(
		"fields" => array
		(
			"version" => $genericInt,
			"views" => $genericInt,
			"hotcount" => $genericInt,			
			"maxusers" => $genericInt,
			"maxusersdate" => $genericInt,
			"maxuserstext" => $text,
			"maxpostsday" => $genericInt,
			"maxpostsdaydate" => $genericInt,
			"maxpostshour" => $genericInt,
			"maxpostshourdate" => $genericInt,
			"milestone" => $text,
		),
	),
	"moodavatars" => array
	(
		"fields" => array
		(
			"id" => $AI,
			"uid" => $genericInt,			
			"mid" => $genericInt,			
			"name" => $var256,
		),
		"special" => $keyID. ", key `mainkey` (`uid`, `mid`)"
	),
	"notifications" => array
	(
		"fields" => array
		(
			"type" => "varchar(16)".$notNull,
			"id" => $genericInt,
			"user" => $genericInt,
			"date" => $genericInt,
			"args" => $var256,
		),
		"special" => "PRIMARY KEY (`type`,`id`,`user`), KEY `type` (`type`), KEY `user` (`user`), KEY `date` (`date`)"
	),
	"passmatches" => array
	(
		"fields" => array
		(
			"date" => $genericInt,
			"ip" => "varchar(50)".$notNull,
			"user" => $genericInt,
			"matches" => "varchar(200)".$notNull,
		),
	),
	"permissions" => array
	(
		"fields" => array
		(
			"applyto" => "tinyint(4) NOT NULL DEFAULT '0'",
			"id" => $genericInt,
			"perm" => "varchar(32)".$notNull,
			"arg" => $genericInt,
			"value" => "tinyint(4) NOT NULL DEFAULT '0'",
		),
		"special" => "PRIMARY KEY (`applyto`,`id`,`perm`,`arg`), KEY `perm` (`perm`,`arg`), KEY `applyto` (`applyto`,`id`), KEY `applyto_2` (`applyto`,`id`,`perm`)"
	),
	"pmsgs" => array
	(
		"fields" => array
		(
			"id" => $genericInt,
			"userto" => $genericInt,
			"userfrom" => $genericInt,
			"conv_start" => $genericInt,
			"date" => $genericInt,
			"ip" => "varchar(45)".$notNull,
			"msgread" => $bool,
			"deleted" => "tinyint(4) NOT NULL DEFAULT '0'",
			"drafting" => $bool,
			"draft_to" => $var128,
		),
		"special" => "key `id` (`id`), key `userto` (`userto`), key `userfrom` (`userfrom`), key `msgread` (`msgread`), key `date` (`date`), key `drafting` (`drafting`)"
	),
	"pmsgs_text" => array
	(
		"fields" => array
		(
			"pid" => $AI,
			"title" => "varchar(256)".$utf8ci.$notNull,
			"text" => $postText,
		),
		"special" => "primary key (`pid`)"
	),
	"poll" => array
	(
		"fields" => array
		(
			"id" => $AI,
			"question" => $var256,
			"closed" => $bool,
			"doublevote" => $bool,
		),
		"special" => $keyID
	),
	"pollvotes" => array
	(
		"fields" => array
		(
			"user" => $genericInt,
			"choiceid" => $genericInt,
			"poll" => $genericInt,
		),
		"special" => "key `lol` (`user`, `choiceid`), key `poll` (`poll`)"
	),
	"poll_choices" => array
	(
		"fields" => array
		(
			"id" => $AI,
			"poll" => $genericInt,
			"choice" => $var256,
			"color" => "varchar(25)".$notNull,
		),
		"special" => $keyID.", key `poll` (`poll`)"
	),
	"posts" => array
	(
		"fields" => array
		(
			"id" => $AI,
			"thread" => $genericInt,
			"user" => $genericInt,
			"date" => $genericInt,
			"ip" => "varchar(45)".$notNull,
			"num" => $genericInt,
			"deleted" => $bool,
			"deletedby" => $genericInt,
			"reason" => "varchar(300)".$notNull,
			"options" => "tinyint(4) NOT NULL DEFAULT '0'",
			"mood" => $genericInt,
			"currentrevision" => $genericInt,
			"has_attachments" => $bool,
		),
		"special" => $keyID.", key `thread` (`thread`), key `date` (`date`), key `user` (`user`), key `ip` (`ip`), key `id` (`id`, `currentrevision`), key `deletedby` (`deletedby`)"
	),
	"posts_text" => array
	(
		"fields" => array
		(
			"pid" => $genericInt,
			"text" => $postText,
			"revision" => $genericInt,
			"user" => $genericInt,
			"date" => $genericInt,
		),
		"special" => "fulltext key `text` (`text`), key `pidrevision` (`pid`, `revision`), key `user` (`user`)"
	),
	"queryerrors" => array
	(
		"fields" => array
		(
			"id" => $AI,		
			"user" => $genericInt,	
			"ip" => "varchar(50)".$notNull,
			"time" => $genericInt,	
			"query" => $text,
			"get" => $text,
			"post" => $text,
			"cookie" => $text,
			"error" => $text
		),
		"special" => $keyID
	),
	"reports" => array
	(
		"fields" => array
		(
			"ip" => "varchar(45)".$notNull,
			"user" => $genericInt,
			"time" => $genericInt,
			"text" => $var1024,
			"hidden" => $bool,
			"severity" => "tinyint(2) NOT NULL DEFAULT '0'",
			"request" => $text,
		),
	),
	"searchcache" => array
	(
		"fields" => array
		(
			"queryhash" => "char(32)".$notNull,
			"query" => $text,
			"date" => $genericInt,
			"threadresults" => $text,
			"postresults" => $text,
		),
		"special" => "PRIMARY KEY (`queryhash`)"
	),
	"secondarygroups" => array
	(
		"fields" => array
		(
			"userid" => $genericInt,
			"groupid" => $genericInt,
		),
		"special" => "PRIMARY KEY (`userid`,`groupid`)"
	),
	"sessions" => array
	(
		"fields" => array
		(
			"id" => $var256,
			"user" => $genericInt,
			"expiration" => $genericInt,
			"autoexpire" => $bool,
			"iplock" => $bool,
			"iplockaddr" => $var128,
			"lastip" => $var128,
			"lasturl" => $var128,
			"lasttime" => $genericInt,
		),
		"special" => $keyID.", key `user` (`user`), key `expiration` (`expiration`)"
	),
	"smilies" => array
	(
		"fields" => array
		(
			"id" => $AI,
			"code" => "varchar(32)".$notNull,
			"image" => "varchar(32)".$notNull,
		),
		"special" => $keyID
	),
	"spieslog" => array
	(
		"fields" => array
		(
			"userid" => $genericInt,
			"date" => $genericInt,
			"pmid" => $genericInt,
		),
		"special" => "KEY `userid` (`userid`), KEY `date` (`date`)"
	),
	"threads" => array
	(
		"fields" => array
		(
			"id" => $AI,
			"forum" => $genericInt,
			"user" => $genericInt,
			"date" => $genericInt,
			"firstpostid" => $genericInt,
			"views" => $genericInt,
			"title" => "varchar(100)".$utf8ci.$notNull,
			"icon" => "varchar(200)".$notNull,
			"replies" => $genericInt,
			"lastpostdate" => $genericInt,
			"lastposter" => $genericInt,
			"lastpostid" => $genericInt,
			"closed" => $bool,
			"sticky" => $bool,
			"poll" => $genericInt,
		),
		"special" => $keyID.", key `forum` (`forum`), key `user` (`user`), key `sticky` (`sticky`), key `lastpostdate` (`lastpostdate`), key `date` (`date`), fulltext key `title` (`title`)"
	),
	"threadsread" => array
	(
		"fields" => array
		(
			"id" => $genericInt,
			"thread" => $genericInt,
			"date" => $genericInt,
		),
		"special" => "primary key (`id`, `thread`)"
	),
	"uploadedfiles" => array
	(
		"fields" => array
		(
			"id" => "char(16)".$notNull,
			"physicalname" => "varchar(64)".$notNull,
			"filename" => "varchar(512)".$notNull,
			"description" => $var1024,
			"user" => $genericInt,
			"date" => $genericInt,
			"parenttype" => "varchar(16)".$notNull,
			"parentid" => $genericInt,
			"downloads" => $genericInt,
			"deldate" => $genericInt,
		),
		"special" => $keyID.", KEY `user` (`user`), KEY `parent` (`parenttype`,`parentid`), KEY `deldate` (`deldate`)"
	),
	// cid = user who commented
	// uid = user whose profile received the comment
	"usercomments" => array
	(
		"fields" => array
		(
			"id" => $AI,
			"uid" => $genericInt,
			"cid" => $genericInt,
			"text" => $text,
			"date" => $genericInt,
		),
		"special" => $keyID.", key `uid` (`uid`), key `date` (`date`)"
	),
	"usergroups" => array
	(
		"fields" => array
		(
			"id" => $genericInt,
			"name" => "varchar(32)".$notNull,
			"title" => $var256, 
			"rank" => $genericInt,
			"type" => "tinyint(4) NOT NULL DEFAULT '0'",
			"display" => "tinyint(4) NOT NULL DEFAULT '0'",
			"color_male" => "varchar(8)".$notNull,
			"color_female" => "varchar(8)".$notNull,
			"color_unspec" => "varchar(8)".$notNull,
		),
		"special" => $keyID
	),
	"users" => array
	(
		"fields" => array
		(
			"id" => $AI,
			"name" => "varchar(32)".$utf8ci.$notNull,
			"displayname" => "varchar(32)".$utf8ci.$notNull,
			"password" => $var256,
			"pss" => "varchar(16)".$notNull,
			"primarygroup" => $genericInt,
			"flags" => "smallint(6) NOT NULL DEFAULT '0'",
			"posts" => $genericInt,
			"regdate" => $genericInt,
			"minipic" => $var128,
			"picture" => $var128,
			"title" => $var256,
			"postheader" => $text,
			"signature" => $text,
			"bio" => $text,
			"sex" => "tinyint(2) NOT NULL DEFAULT '2'",
			"rankset" => $var128,
			"realname" => "varchar(60)".$notNull,
			"lastknownbrowser" => $var1024,
			"location" => $var128,
			"birthday" => $genericInt,
			"email" => "varchar(60)".$utf8ci.$notNull,
			"homepageurl" => "varchar(80)".$notNull,
			"homepagename" => "varchar(100)".$notNull,			
			"lastposttime" => $genericInt,
			"lastactivity" => $genericInt,
			"lastip" => "varchar(50)".$notNull,
			"lasturl" => $var128,
			"lastforum" => $genericInt,
			"postsperpage" => "int(8) NOT NULL DEFAULT '20'",
			"threadsperpage" => "int(8) NOT NULL DEFAULT '50'",
			"timezone" => "float NOT NULL DEFAULT '0'",
			"theme" => "varchar(64)".$notNull,
			"signsep" => $bool,
			"dateformat" => "varchar(20) NOT NULL DEFAULT 'm-d-y'",
			"timeformat" => "varchar(20) NOT NULL DEFAULT 'h:i a'",
			"fontsize" => "int(8) NOT NULL DEFAULT '80'",
			"karma" => $genericInt,
			"blocklayouts" => $bool,
			"globalblock" => $bool,
			"fulllayout" => $bool,
			"showemail" => $bool,
			"lastprofileview" => $genericInt,
			"tempbantime" => $hugeInt,
			"tempbanpl" => $genericInt,
			"pluginsettings" => $text,
			"lostkey" => $var128,
			"lostkeytimer" => $genericInt,
			"loggedin" => $bool,
		),
		"special" => $keyID.", key `posts` (`posts`), key `name` (`name`), key `lastforum` (`lastforum`), key `lastposttime` (`lastposttime`), key `lastactivity` (`lastactivity`), key `lastip` (`lastip`)"
	),
);
?>
