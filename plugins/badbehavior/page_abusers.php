<?php

$title = "Service Abusers";

require 'bad-behavior/responses.inc.php';

if ($loguser['powerlevel'] < 3)
	Kill('No.');

echo "
	<table class=\"outline margin width100\">
		<tr class=\"header0\">
			<th>
				Date
			</th>
			<th>
				IP
			</th>
			<th>
				Request
			</th>
			<th>
				Key
			</th>
		</tr>
";

$abusers = query('SELECT * FROM {bad_behavior} ORDER BY `date` DESC');
while ($abuser = fetch($abusers))
{
	$date = formatdate(strtotime($abuser['date']));

	$response = bb2_get_response($abuser['key']);
	echo "
		<tr class=\"cell0\">
			<td>
				{$date}
			</td>
			<td>
				".formatIP($abuser['ip'])."
			</td>
			<td>
				<pre style='white-space:pre-wrap'>". htmlspecialchars(preg_replace('/logsession=\w+/', 'logsession=?????', $abuser['http_headers'])) . "</pre>
			</td>
			<td>
				<abbr title=\"" . htmlspecialchars($response['log']). "\">{$abuser['key']}</abbr>
			</td>
		</tr>
";
}

echo "
	</table>
";

?>
