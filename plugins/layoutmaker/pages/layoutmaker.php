<?php

include("plugins/layoutmaker/bases/list.php");
$offers = "";
foreach($bases as $base)
{
	$picture = '';
	if (file_exists('plugins/layoutmaker/bases/'.$base['name'].'.png'))
		$picture = "<img src=\"".resourceLink("plugins/layoutmaker/bases/".$base['name'].".png")."\" />";
	$cellClass = ($cellClass + 1) % 2;
	$offers .= "
	<tr class=\"cell$cellClass\">
		<td>
			$picture
		</td>
		<td>
		".actionLinkTag($base['title'], "layoutmaker2", $base['name'])."
		</td>
		<td>
			".$base['description']."
		</td>
	</tr>";
}

write(
"
<table class=\"outline margin width50\">
	<tr class=\"header1\">
		<th>Preview</th>
		<th>Name</th>
		<th>Description</th>
	</tr>
	{0}
</table>
<p>
Obvious problems I'm well aware of that you shouldn't bother bugging me about:
<ul>
	<li>Introduction page (this one) sucks balls.</li>
	<li>Sliders don't have buddy ranges</li>
	<li>The CGA palette in the color picker doesn't trigger the auto-update.</li>
	<li>Layouts only work on ABXD and Neritic Net, and not on Board2.</li>
</ul>
</p>
",	$offers);


