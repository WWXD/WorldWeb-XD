<?php
if($loguser['powerlevel'] < 2)
	Kill(__("You're not admin. There is nothing for you here."));

$crumbs = new PipeMenu();
$crumbs->add(new PipeMenuLinkEntry(__("Admin"), "admin"));
$crumbs->add(new PipeMenuLinkEntry(__("Update board"), "gitpull"));
makeBreadcrumbs($crumbs);

$output = array();
exec("git pull 2>&1", $output);
echo '<div style="width: 50%; margin-left: auto; margin-right: auto; background: black; border: 1px solid #0f0; color: #0f0; font-family: \'Consolas\', \'Lucida Console\', \'Courier New\', monospace;">';

if (empty($output)) echo '<em>(no output)</em>';
else
	foreach ($output as $line) echo htmlspecialchars($line).'<br>';

echo '</div>';

?>
