<?php

if(str_word_count($_POST["text"]) < Settings::pluginGet("minwords"))
{
	Alert(__("If you have nothing interesting to post, just don't post."), __("Your post is too short."));
	$rejected = true;
}
