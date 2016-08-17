<?php

function printGitCommit()
{
	exec("git rev-parse --short HEAD", $output);
	print "Git revision ";
	print trim($output[0]);
	print ", branch ";
	$output = NULL;
	exec("git symbolic-ref HEAD", $output);
	$output = explode("/", $output[0]);
	print($output[sizeof($output)-1]);
}

printGitCommit();
print "<br />";
