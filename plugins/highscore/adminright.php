<?php

	$df = "l, F jS Y, G:i:s";

	cell2(format(
"
			Highest number of users in five minutes
			</td>
			<td>
				{0}, on {1} GMT:<br />
				{2}
", $misc['maxusers'], gmdate($df, $misc['maxusersdate']), $misc['maxuserstext']));

?>
