<?php
if (!defined('BLARG')) die();

$credits = ('
	This board software was created by the following contributors, in no special order.<br>
	<ul>
		<li>Kawa — Originally created Acmlmboard XD ("ABXD")</li>
		<li>Dirabio — Contributed to ABXD 3.0, which parts of this board are based off of</li>
		<li>StapleButter — Created Blargboard (a fork of Acmlmboard XD)</li>
		<li>Maorninja322 — Created WorldWeb XD (a fork of Blargboard), coder</li>
		<li>JeDaYoshi — Ported all the Acmlmboard XD plugins to Blargboard, ported the RPG, coder</li>
		<li>Phase — Added compatibility to 5.7 MySQL databases, JSON ranksets, etc...</li>
		<li>MoonlightCapital / Super-toad 65 - Managing the Wiki for me, making a hypersticky function (which is the base of the new "Sticky level" thingy that I did, etc...</li>
		<li>Repflez - Fixed bugs, and made a private fork of Blargboard, which got merged here, did a full URL rewritting system, etc...</li>
		<li>DankMemeItTheFrog - Made Instameme, and ported the Layout Maker, while adding some of his changes.</li>
		<li>LifeMushroom — Themes</li>
		<li>Everyone behind <a href="https://fortawesome.github.io/Font-Awesome/">Font Awesome</a>, <a href="https://jquery.com/">jQuery</a>, Smarty, and any other libraries this software uses</li>
	</ul>');

RenderTemplate('credits', array('credits' => $credits));
