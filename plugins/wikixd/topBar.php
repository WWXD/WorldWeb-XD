<?php

if (!defined('WIKIXD')) return;

$tools = new PipeMenu();
$tools->add(new PipeMenuLinkEntry('Recent changes', 'wikichanges'));
if ($canedit) $tools->add(new PipeMenuLinkEntry('Create page', 'wikiedit', '', 'createnew'));
$tools->add(new PipeMenuLinkEntry('Random page', 'wikirandom'));

echo '
		<table class="outline margin">
			<tr class="header1"><th>Wiki tools</th></tr>
			<tr class="cell1 center"><td>'.$tools->build().'</td></tr>
		</table>';

?>