# WorldWeb XD

WorldWeb XD is a website maker written in PHP. It uses MySQL for Database storage, Smarty for its templates (might change), Font Awesome for the icons and jQuery.

## Requirements

PHP version (minimum): 5.5. It will work on 7.0.    
PHP extentions: mcrypt & PHP-GD

You will also need `mod-rewrite` for apache2 and allow `override all` for rewritten URLS

Knowledge in PHP: No, but if you do, you can add your own nifty features. Just be sure to send in a pull request, so other users can use it as well.

MySQL minimum: None, but you should have a recent version. We might make it to be MySQL 5.6 for a better IP checker but most likely not, because it isn't widely available.

MariaDB also works with this.


## How to install and use

1. Go to any webhost of your desire (if you want a freehost, we recommend [h05t.gq](http://h05t.gq/)) and sign up. You should be getting an email with CPanel info.
2. Download WorldWeb XD. If you want some stable software, go to the release page and press download for the .zip files
3. Get the FTP data of your new host and upload all the files there. You can use FileZilla to do so.
4. Make a MySQL database, and take notes of needed info.
5. Go to your domain and follow the on-screen prompts.

If everything went fine, browse to your freshly installed website and configure it. If not, let us know.

We recommend you take some time and make your own website themes and banner to give your board a truly unique feel.

If you want to have a image logo, just be sure to put it in the `img` directory, under the name `logo.png`.

If you want addons, they're here: https://github.com/WorldWeb-XD/Addons      
If you want themes, they're here: https://github.com/WorldWeb-XD/Themes     
If you want ranksets, they're here: https://github.com/WorldWeb-XD/Ranksets

## How to update your website

1. Download the most recent WorldWeb XD package (be it an official release or a Git package).
2. Copy the files over your existing board's files.
WARNING: Make sure to not overwrite/delete the config directory, especially config/salt.php! Lose that one and you'll have fun resetting everyone's passwords.
Everything else is safe to overwrite.
3. Check your original install.sql and the new one.
4. Make the nessesary changes on phpmyadmin accordingly

## Features

 * Flexible permission system
 * Add-on system
 * Templates (in the works, about 80% done). This uses the Smarty system, though, we would like to change that to something else.
 * URL rewriting, enables human-readable forum and thread URLs for public content
 * Post layouts
 * more Acmlmboard feel
 * typical messageboard features
 * Smiley Box.
 * Instameme (thanks Jon)

## Website owner's tips

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

If anything goes wrong with your board, report it either on our [Github's issues page](https://github.com/WWXD/WorldWeb-XD/issues), [Gitter](https://gitter.im/WWXD/Lobby?utm_source=share-link&utm_medium=link&utm_campaign=share-link) or our [Discord Server](https://discord.gg/t52Tgvt). Make sure to describe your problems in detail.

If the error is a 'MySQL Error', to get a detailed report, you need to open config/database.php in a text editor, find `$debugMode = 0;` and replace it with `$debugMode = 1;`. 
This will make the board give you the MySQL error message and the query which went wrong. After that, report the error to us and we'll fix it. Once you're done troubleshooting your board, it is recommended that you edit config/database.php back so that `$debugMode` is 0.

YOU WILL NOT RECEIVE HELP IF YOU HAVEN'T READ THE INSTRUCTIONS WHEN INSTALLING YOUR BOARD.

## TODO list

https://github.com/WWXD/WorldWeb-XD/projects/1
 
## Credits

http://maorninja.h05t.gq/credits

Edit: I need to add @Repflez to the page, but I don't have the database password anymore, so once I get the database password back (max: next week), I'll make sure to credit him on the page.

-------------------------------------------------------------------------------

Have fun. Thanks for using WorldWeb XD.
