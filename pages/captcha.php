<?php

$ajaxPage = true;

/* To install the captcha, download Securimage from
 *
 *	--->	http://www.phpcaptcha.org	<---
 *
 * and extract it into a /securimage folder.
 */

include 'securimage/securimage.php';

$img = new securimage();

$img->image_width = 200;
$img->image_height = 80;

$img->image_bg_color = new Securimage_Color(0x00, 0x00, 0x00);
$img->line_color = new Securimage_Color(0x6d, 0x6d, 0x6d);
$img->text_color = new Securimage_Color(0xFF, 0x6d, 0x6d);
$img->num_lines = 7;

// lowercase letters and numbers excluding ambiguous characters
$img->charset = 'abcdefghklmnprstuvwyz23456789';

//This is basically the default setting, but in the Securimage directory instead of the board root.
$img->ttf_file = "securimage/AHGBold.ttf";

$img->show('');