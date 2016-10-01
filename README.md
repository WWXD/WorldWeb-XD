# Blargboard XD

https://www.3dshacks.xyz/forum/1-support/

-------------------------------------------------------------------------------

Board software written in PHP. Uses MySQL for storage.

This is the software that powers 3DS Hacks (https://www.3dshacks.xyz), well,
excluding the 3DS Hacks-specific modifications.

It is based off ABXD. ABXD is made by Dirbaio, Nina, GlitchMr & co, and was originally
Kawa's project. See http://abxd.dirbaio.net/ for more details.

It uses Smarty for its templates, and Font Awesome. And possibly some other funny things 
I forgot about.

This board follows the Acmlmboard concept. If you don't know Acmlmboard, you don't know what you're missing.

## Requirements

Blargboard XD requires PHP 5.3. You also need the mcrypt extension.

There is no exact requirement for MySQL, but make sure to have a recent version.

Everything else is provided in the package.

## How to install and use

PHP and MySQL knowledge isn't required to use Blargboard XD but is a plus.

Get a webserver. Upload the Blargboard XD codebase to it. Create an empty MySQL database.

Browse to your board's install.php (http://my.board.for.example/install.php) and follow the instructions.

If everything went fine, browse to your freshly installed board and configure it. If not, let us know.

We recommend you take some time and make your own board themes and banner to give your board a truly unique feel.
If you have HTML knowledge, you can even edit the templates to change your board's look more in-depth.

## How to update your board

Download the most recent Blargboard package (be it an official release or a Git package).

Copy the files over your existing board's files.

Make sure to not overwrite/delete the config directory, especially config/salt.php! Lose that one and you'll have fun resetting everyone's passwords.
Everything else is safe to overwrite. Be careful to not lose any changes you have made, though.

Once that is done, run update.php (http://my.board.for.example/update.php) to update the board's database structure.

Depending on the versions, your update may involve running extra scripts to fix certain things. Make sure to follow those instructions.

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

-------------------------------------------------------------------------------

Coders and such, who like to hack new features in their software, may think that the use
of templates in Blargboard XD gets in their way. Well uh, can't please everybody. I tried to
do my best at separating logic and presentation. Besides, the use of templates actually
makes the code nicer. Just look at the first few revisions and see how much duplicate logic
is powering the mobile layout, for example. Templates allowed to get rid of all that madness.

As of now, there are no official releases for this, and the ABXD database installer hasn't
been adapted to Blargboard XD's database structure yet. Thus, when updating your Blargboard XD
copy, you need to check for changes to database.sql and modify your database's structure
accordingly.

## Board owner's tips

http://board.example/?page=makelr -> regenerates the L/R tree used for forum listings and such.
Use if some of your forums are showing up in wrong places.

http://board.example/?page=editperms&gid=X -> edit permissions for group ID X.

http://board.example/?page=secgroups -> assign secondary groups to a user.


How to add groups: add to the usergroups table via PMA
 * type: 0 for primary groups, 1 for secondary
 * display: 0 for normal group, 1 for group listed as staff, -1 for hidden group
 * rank: a user may not mess with users of higher ranks no matter his permissions

 
How to add/remove secondary groups to someone: add to/remove from the secondarygroups table via PMA (or use ?page=secgroups for adding)
 * userid: the user's ID
 * groupid: the group's ID. Do not use the ID of a primary group!
 
WARNING: when banning someone, make sure that the secondary groups' permissions won't override the banned group's permissions. If that happens, you'll need to delete the secondarygroups assignments for the user.


How to (insert action): first look into your board's admin panel, settings panel, etc... then if you still can't find, ask us. But please don't be a noob and ask us about every little thing.

## Support, troubleshooting, etc

The Blargboard help forum is at 3DS Hacks: http://3dshacks.rf.gd/forum/1-support/

If anything goes wrong with your board, go there and let us know. Make sure to describe your problems in detail, our crystal ball is scratched so we can't see well.

If the error is a 'MySQL Error', to get a detailed report, you need to open config/database.php in a text editor, find `$debugMode = 0;` and replace it with `$debugMode = 1;`. 
This will make the board give you the MySQL error message and the query which went wrong. Once you're done troubleshooting your board, it is recommended that you edit config/database.php back so that `$debugMode` is 0.

YOU WILL NOT RECEIVE HELP IF YOU HAVEN'T READ THE INSTRUCTIONS WHEN INSTALLING YOUR BOARD.

## TODO list

(no particular order there)

 * Subtitles
 * Multiple board listing
 * A Better post toolbar
 * SubTitles
 * Better Mobile layout
 * Makes Favorites usable
 * make userlinks usable
 * Multiple Forum listing layout
 * Multiple Board layouts
 * Switch from logo to text anytime
 * Make filter posts anytime without editing the lib folder
 * Add multiple 'boards'
 * Add categories in the categories uploader without SQL
 * Add events in the calendar
 * Add "moderation" like style for normal users.
 * Add friends/foes list (for automatically setting above)
 * Others here.
 
## Credits

 * *StapleButter* - Original Blargboard we all love.
 * *Maorninja* - Leader, coder.
 * *Mariogamer* - Uploading original Blargboard.
 * *Phase* - JSON ranksets, animation mobile, fix MySQL5.7 support and Gist tab.
 * *JeDa* - Plugins, coding.
 * *LifeMushroom* - Themes.

-------------------------------------------------------------------------------

Blargboard XD is provided as-is, with no guarantee that it'll be useful or even work. I'm not
responsible if it explodes in your face. Use that thing at your own risk.

Oh well, it should work rather well. See 3DS Hacks. But uh, we never know.

-------------------------------------------------------------------------------

Have fun.

blarg
