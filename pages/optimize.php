<?php
if (!defined('BLARG')) die();

if(!$loguser['root'])
	Kill(__("You're not an administrator. There is nothing for you here."));

MakeCrumbs(array(actionLink("admin") => __("Admin"), actionLink("optimize") => __("Optimize tables")));

$rStats = Query("show table status");
while($stat = Fetch($rStats))
	$tables[$stat['Name']] = $stat;

$tablelist = "";
$total = 0;
foreach($tables as $table)
{
	$cellClass = ($cellClass+1) % 2;
	$overhead = $table['Data_free'];
	$total += $overhead;
	$status = __("OK");
	if($overhead > 0)
	{
		Query("OPTIMIZE TABLE `{".$table['Name']."}`");
		$status = "<strong>".__("Optimized")."</strong>";
	}

	$tablelist .= format(
"
	<tr class=\"cell{0}\">
		<td class=\"cell2\">{1}</td>
		<td>
			{2}
		</td>
		<td>
			{3}
		</td>
		<td>
			{4}
		</td>
	</tr>
",	$cellClass, $table['Name'], $table['Rows'], $overhead, $status);
}

write(
"
<table class=\"outline margin\">
	<tr class=\"header0\">
		<th colspan=\"7\">
			".__("Table Status")."
		</th>
	</tr>
	<tr class=\"header1\">
		<th>
			".__("Name")."
		</th>
		<th>
			".__("Rows")."
		</th>
		<th>
			".__("Overhead")."
		</th>
		<th>
			".__("Final Status")."
		</th>
	</tr>
	{0}
	<tr class=\"header0\">
		<th colspan=\"7\" style=\"font-size: 130%;\">
			".__("Excess trimmed: {1} bytes")."
		</th>
	</tr>
</table>

", $tablelist, $total);

?>
