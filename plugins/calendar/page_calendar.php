<?php

$title = __("Calendar");

$now = getdate(time());
$year = $now['year'];
$month = $now['mon'];
$day = $now['mday'];

if((int)$_GET['month'])
{
	$month = (int)$_GET['month'];
	$day = 0;
}

$d = getdate(mktime(0, 0, 0, $month, 1, $year));
$i = 1 - $d['wday'];
$d = getdate(mktime(0, 0, 0, $month + 1, 0, $year));
$max = $d['mday'];

$users = Query("select u.(_userfields), u.birthday as u_birthday from {users} u where birthday != 0 order by name");
$cells = array();
while($user = Fetch($users))
{
	$user = getDataPrefix($user, "u_");
	$d = getdate($user['birthday']);
	if($d['mon'] == $month)
	{
		$dd = $d['mday'];
		$age = $year - $d['year'];
		$cells[$dd] .= "<br />&bull; ".format(__("{0}'s birthday ({1})"), Userlink($user), $age)."\n";
	}
}

if ($year == 2012 && $month == 12)
{
	$cells[21] .= '<br>&bull; <span style="color:red;font-weight:bold;text-decoration:blink;">THE END OF THE WORLD</span>';
	$cells[22] .= '<br>&bull; Laugh at those who actually believed it';
}

$cellClass = 0;
while($i <= $max)
{
	$grid .= format(
"
	<tr>
");
	for($dn = 0; $dn <= 6; $dn++)
	{
		$dd = $i + $dn;
		if($dd < 1 || $dd > $max)
			$label = "";
		else
			$label = format(
"
			{0}
			{1}", $dd, $cells[$dd]);
		$grid .= format(
"
		<td class=\"cell{2} smallFonts\" style=\"height: 80px; vertical-align: top;\">
			{1}
		</td>
",	$cellClass, $label, ($label == "" ? 1 : 0));
		$cellClass = ($cellClass+1) % 2;
	}
	$grid .= format(
"
	</tr>
");
	$i += 7;
}

$monthChoice = "";
for($i = 1; $i <= 12; $i++)
{
	if($i == $month)
	{
		$monthChoice .= format("<li>{0}</li>", $months[$i]);
	}
	else
	{
		$monthChoice .= actionLinkTagItem($months[$i], "calendar", 0, "month=$i");
	}
}

write(
"
<table class=\"outline margin\">
	<tr class=\"header0\">
		<th colspan=\"7\">
			{0} {1}
		</th>
	</tr>
	<tr class=\"header1\">
		<th {3}>".$days[1]."</th>
		<th {3}>".$days[2]."</th>
		<th {3}>".$days[3]."</th>
		<th {3}>".$days[4]."</th>
		<th {3}>".$days[5]."</th>
		<th {3}>".$days[6]."</th>
		<th {3}>".$days[7]."</th>
	</tr>
	{2}
	<tr>
		<td class=\"cell2 smallFonts center\" colspan=\"7\">
			<ul class=\"pipemenu\">
				{4}
			</ul>
		</td>
	</tr>
</table>
",	$months[$month], $year, $grid, "style=\"width: 14.3%;\"", $monthChoice);

?>
