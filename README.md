# WorldWeb XD

WorldWeb XD is a website maker written in PHP. It uses MySQL for Database storage, Smarty for its templates, Font Awesome for the icons and jQuery.

# This will not get any new features for a while, as currently, we are trying to fix our current issues with WWXD before continuing.

## Requirements

PHP version (minimum): 5.5. It will work on 7.0.    
PHP extentions: mcrypt & PHP-GD

You will also need `mod-rewrite` for apache2 and allow `override all` for rewritten URLS

Knowledge in PHP: No, but if you do, you can add your own nifty features. Just be sure to send in a pull request, so other users can use it as well.

MySQL/MariaDB minimum: None, but you should have a recent version. We might make it to be MySQL 5.6 for a better IP checker but most likely not, because it isn't widely available yet.

## Simularities and differences between this, ABXD and Blargboard

Disclaimer: I'm not trying to bash ABXD or Blargboard. I'm just posting the differences between the 3. Besides, WWXD started from these two, so if I bash them, I'm also bashing what started WWXD.

<table>
<tr><th></th><th>ABXD</th><th>Blargboard</th><th>WorldWeb XD (with the forums plugin)</th></tr>
<tr><th>Templates</th>
	<td style="text-align: center;">No</td>
	<td style="text-align: center;" colspan=2>Yes, using Smarty</td>
</tr>
<tr><th>Groups and Permissions</th>
	<td style="text-align: center;">Global Permissions (not customizable), forced 5 powerlevels</td>
	<td style="text-align: center;" colspan=2>Role-based, (sub)forum-based and user-based permissions</td>
</tr>
<tr><th>Peer Ratings</th>
<td>Yes</td>
<td colspan=2>No</td>
</tr>
<tr><th>Syndrones (classic acmlmboard feature)</th>
<td colspan=2>Classic</td>
<td>Classic, Netric Net, acmlmboard 2.0, vizzed</td>
</tr>
<tr><th>Password</th>
	<td style="text-align: center;">SHA256 with Global & User Salt, included password checker</td>
	<td style="text-align: center;">SHA256 with Global & User Salt</td>
	<td style="text-align: center;">PHP 5.5 password_hash, included password checker and generator.</td>
</tr>
<tr><th>Change Main Layout</th>
	<td style="text-align: center;">Yes, using settings</td>
	<td style="text-align: center;">You must edit the files</td>
	<td style="text-align: center;">Yes, using settings</td></tr>
<tr><th>Ranksets</th>
	<td style="text-align: center;" colspan=2>Yes, PHP</td>
	<td style="text-align: center;">Yes, JSON or PHP</td></tr>
<tr><th>Bump threads</th>
	<td style="text-align: center;" colspan=2>You must post in them</td>
	<td style="text-align: center;">Just click the "Bump" button</td></tr>
<tr><th>Sticky</th>
	<td style="text-align: center;" colspan=2>Only 2</td>
	<td style="text-align: center;">Multiple levels...</td></tr>
<tr><th>Thread description</th>
	<td style="text-align: center;" colspan=2>No</td>
	<td style="text-align: center;">Yes</td></tr>
<tr><th>Permanently delete from database</th>
	<td style="text-align: center;">You can delete users (plugin needed)</td>
	<td style="text-align: center;">ABXD plugin broken, needs port...</td>
	<td style="text-align: center;">You can delete posts and users.</td></tr>
<tr><th>Captcha</th>
	<td style="text-align: center;">PHPCaptcha (need external files) (Optional)</td>
	<td style="text-align: center;">KuriChallenge (broken, according to darkeater38) (intergrated) (forced)</td>
	<td style="text-align: center;">PHPCaptcha and BotCaptcha (need external files) (optional)</td></tr>
<tr><th>Proxy Protection</th>
	<td style="text-align: center;">None</td>
	<td style="text-align: center;">StopForumSpam, checks IPs (broken, uses old URL)</td>
	<td style="text-align: center;">StopForumSpam, checks IPs and emails</td></tr>
</table>

## How to install and use

1. Go to any webhost of your desire and sign up. You should be getting an email with CPanel info. You can skip this step if you already have one. We suggest [InfinityFree](https://infinityfree.net/) if you are searching a free one.
2. Download WorldWeb XD. If you want some stable software, go to the Releases page and press Download for the .zip file.
3. Get the FTP data of your host and upload all the files there. You can use FileZilla to do so.
4. Make a MySQL database, and take notes of needed info.
5. Go to your domain and follow the on-screen prompts.

If everything went fine, browse to your freshly installed website and configure it. If not, let us know.

We recommend you take some time and make your own website themes and banner to give your board a truly unique feel.

If you want to have a image logo, just be sure to put it in the `img` directory, under the name `logo.png`.

If you want addons (that are currently unstable), they're there: https://github.com/WWXD/Add-ons-unstable     
If you want themes, they're there: https://github.com/WWXD/Themes
If you want ranksets, they're there: https://github.com/WWXD/Ranksets

## How to update your website

1. Download the most recent WorldWeb XD package (be it an official release or a Git package).
2. Copy the files over your existing board's files. (WARNING: Make sure to not overwrite/delete the config directory, especially config/salt.php! Lose that one and you'll have fun resetting everyone's passwords. Be sure to backup your logo and/or icon on img/, either way! Everything else is safe to overwrite.)
3. Check your original install.sql and the new one.
4. Make the neccesary changes on phpMyAdmin accordingly.

## Features

 * Flexible permission system
 * Add-on system
 * Templates (WIP, about 80% done).
 * URL rewriting, enables human-readable forum and thread URLs for public content
 * Post layouts
 * more Acmlmboard feel
 * typical messageboard features
 * Smiley Box.
 * Instameme

## Website owner's tips

How to add groups: add to the usergroups table via PMA
 * type: 0 for primary groups, 1 for secondary
 * display: 0 for normal group, 1 for group listed as staff, -1 for hidden group
 * rank: a user may not mess with users of higher ranks no matter his permissions

 
How to add/remove secondary groups to someone: add to/remove from the secondarygroups table via PMA
 * userid: the user's ID
 * groupid: the group's ID. Do not use the ID of a primary group!

WARNING: when banning someone, make sure that the secondary groups' permissions won't override the banned group's permissions. If that happens, you'll need to delete the secondarygroups assignments for the user.

How to (insert action): first look into your board's admin panel, settings panel, etc... then if you still can't find, ask us. But please don't be a noob and ask us about every little thing. Also, please take note that the action you want to do might not be in WorldWeb XD.

## Support, troubleshooting, etc

If anything goes wrong with your board, report it either on our [Github's issues page](https://github.com/WWXD/WorldWeb-XD/issues), [Gitter](https://gitter.im/WWXD/Lobby?utm_source=share-link&utm_medium=link&utm_campaign=share-link) or our [Discord Server](https://discord.gg/t52Tgvt). Make sure to describe your problems in detail.

If the error is a 'MySQL Error', to get a detailed report, you need to open config/database.php in a text editor, find `$debugMode = 0;` and replace it with `$debugMode = 1;`. 
This will make the board give you the MySQL error message and the query which went wrong. After that, report the error to us and we'll fix it. Once you're done troubleshooting your board, it is recommended that you edit config/database.php back so that `$debugMode` is 0.

YOU WILL NOT RECEIVE HELP IF YOU HAVEN'T READ THE INSTRUCTIONS WHEN INSTALLING YOUR BOARD.

## TODO list

https://github.com/WWXD/WorldWeb-XD/projects/1
 
## Credits

<table>
	<tr><th>Credits</th></tr>
	<tr><td>
		This software was created by the following contributors, in no special order.<br>
	<ul>
		<li>Kawa — Originally created Acmlmboard XD ("ABXD")</li>
		<li>Dirbaio — Contributed to ABXD 3.0, which parts of this board are based off of</li>
		<li>StapleButter — Created Blargboard (a fork of Acmlmboard XD)</li>
		<li>Maorninja322 — Created WorldWeb XD (a fork of Blargboard), coder</li>
		<li>MoonlightCapital — Made a password generator.</li>
		<li>JeDaYoshi — Ported all the Acmlmboard XD plugins to Blargboard, ported the RPG, coder</li>
		<li>Phase — Added compatibility to 5.7 MySQL databases, JSON ranksets, etc...</li>
		<li>Repflez - Fixed bugs, and made a private fork of Blargboard, which got merged here, did a full URL rewritting system, etc...</li>
		<li>DankMemeItTheFrog - Made Instameme, and ported the Layout Maker, while adding some of his changes.</li>
		<li>LifeMushroom — Themes</li>
		<li>Everyone behind <a href="https://fortawesome.github.io/Font-Awesome/">Font Awesome</a>, <a href="https://jquery.com/">jQuery</a>, Smarty, and any other libraries this software uses</li>
	</ul>
	</td></tr>
</table>

-------------------------------------------------------------------------------

Have fun. Thanks for using WorldWeb XD.
