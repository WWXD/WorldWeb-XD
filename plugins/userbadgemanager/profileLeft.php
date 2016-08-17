<?php

die('fixme');
if($loguser['powerlevel'] > 3)
{
	Write("
				<table class=\"outline margin\" style=\"margin-top:1.5em;\">
					<tr class=\"header0\">
						<th colspan=\"2\">" . __("Badge Manager Panel") . "</th>
					</tr>
							<tr>
								<td class=cell0 align=\"center\">
									<a href=\"".actionLink("userbadges", "", "userid=" . $id ."&action=newbadge")."\">" . __("Add a new badge for this user") . "</a>
									&nbsp;&mdash;&nbsp;
									<a href=\"".actionLink("userbadges", "", "userid=" . $id ."&action=deleteall")."\">" . __("Delete all badges of this user") . "</a>
								</td>
							</tr>
				</table>");
}
?>