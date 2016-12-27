# WorldWeb XD

https://maorninja.h05t.gq/

-------------------------------------------------------------------------------

WorldWeb XD is a website maker written in PHP. It uses MySQL for Database storage

It is based off ABXD. ABXD is made by Dirbaio, Nina, GlitchMr & co, and was originally
Kawa's project. See http://abxd.dirbaio.net/ for more details.

It uses Smarty for its templates, Font Awesome for the icons and jQuery. And possibly some other funny things 
I forgot about.

## Requirements

WorldWeb XD requires PHP 5.3. You also need the mcrypt extension.

There is no exact requirement for MySQL, but make sure to have a recent version.

Everything else is provided in the package.

## How to install and use

PHP and MySQL knowledge isn't required to use WorldWeb XD but is a plus.

Get a webserver. Upload the WorldWeb XD codebase to it. Create an empty MySQL database.

Browse to your websites's link and follow the instructions.

If everything went fine, browse to your freshly installed board and configure it. If not, let us know.

We recommend you take some time and make your own board themes and banner to give your board a truly unique feel.

If you can't make a board banner, delete img/logo.png to have a text banner.

If you want plugins, they're here: https://github.com/WorldWeb-XD/Plugins      
If you want themes, they're here: https://github.com/WorldWeb-XD/Themes     
If you want ranksets, they're here: https://github.com/WorldWeb-XD/Ranksets

## How to update your website

1. Download the most recent Blargboard package (be it an official release or a Git package).
2. Copy the files over your existing board's files.
WARNING: Make sure to not overwrite/delete the config directory, especially config/salt.php! Lose that one and you'll have fun resetting everyone's passwords.
Everything else is safe to overwrite.
3. Check your original install.sql and the new one.
4. Make the nessesary changes on phpmyadmin accordingly

## Features

 * Flexible permission system
 * Plugin system
 * Templates (in the works, about 80% done)
 * URL rewriting, enables human-readable forum and thread URLs for public content
 * Post layouts
 * more Acmlmboard feel
 * typical messageboard features
 * Smiley Box.
 * Instameme (thanks Jon)

## Board owner's tips

http://board.example/?page=makelr -> regenerates the L/R tree used for forum listings and such.
Use if some of your forums are showing up in wrong places.

http://board.example/?page=editperms&gid=X -> edit permissions for group ID X.

http://board.example/?page=secgroups -> assign secondary groups to a user.


How to add groups: add to the usergroups table via PMA
 * type: 0 for primary groups, 1 for secondary
 * display: 0 for normal group, 1 for group listed as staff, -1 for hidden group
 * rank: a user may not mess with users of higher ranks no matter his permissions

 
How to add/remove secondary groups to someone: add to/remove from the secondarygroups table via PMA
 * userid: the user's ID
 * groupid: the group's ID. Do not use the ID of a primary group!

WARNING: when banning someone, make sure that the secondary groups' permissions won't override the banned group's permissions. If that happens, you'll need to delete the secondarygroups assignments for the user.

How to (insert action): first look into your board's admin panel, settings panel, etc... then if you still can't find, ask us. But please don't be a noob and ask us about every little thing.

## Support, troubleshooting, etc

The WorldWen help forum is at my devboard: http://maorninja.h05t.gq

If anything goes wrong with your board, go there and let us know. Make sure to describe your problems in detail.

If the error is a 'MySQL Error', to get a detailed report, you need to open config/database.php in a text editor, find `$debugMode = 0;` and replace it with `$debugMode = 1;`. 
This will make the board give you the MySQL error message and the query which went wrong. After that, report the error to us and we'll fix it. Once you're done troubleshooting your board, it is recommended that you edit config/database.php back so that `$debugMode` is 0.

YOU WILL NOT RECEIVE HELP IF YOU HAVEN'T READ THE INSTRUCTIONS WHEN INSTALLING YOUR BOARD.

## TODO list

https://github.com/WWXD/WorldWeb-XD/projects/1
 
## Credits

http://maorninja.h05t.gq/credits

-------------------------------------------------------------------------------

Have fun. Thanks for using WorldWeb XD.