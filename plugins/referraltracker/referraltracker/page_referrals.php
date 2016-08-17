<?php

$title = 'Referrals';

$crumbs = new PipeMenu();
$crumbs->add(new PipeMenuLinkEntry(__("Admin"), "admin"));
$crumbs->add(new PipeMenuLinkEntry(__("Referrals"), "referrals"));
makeBreadcrumbs($crumbs);

echo 
'<table class="outline margin">
	<tr class="header1"><th>URL</th><th>Hit count</th></tr>
';

$refs = Query("SELECT referral,count FROM {referrals} ORDER BY count DESC LIMIT 200");
if (!NumRows($refs))
	echo '	<tr class="cell0"><td colspan="2">No referrals recorded.</td></tr>
';
else
{
	$c = 0;
	while ($ref = Fetch($refs))
	{
		echo '	<tr class="cell',$c,'"><td>',htmlspecialchars($ref['referral']),'</td><td class="center">',$ref['count'],'</td></tr>
';
		
		$c = 1-$c;
	}
}

echo
'</table>
';

?>
