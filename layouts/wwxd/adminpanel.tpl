	<table class="outline margin admininfo width50" style="float: right;">
		<tr class="header1">
			<th colspan="2">
				Information
			</th>
		</tr>
		{foreach $adminInfo as $label=>$contents}
		<tr class="cell{cycle name='admininfo' values='0,1'}">
			<td class="cell2 center">
				{$label}
			</td>
			<td>
				{$contents}
			</td>
		</tr>
		{/foreach}
	</table>
	
	<table class="outline margin adminlinks width25">
		<tr class="header1">
			<th>
				Administration tools
			</th>
		</tr>
		{foreach $adminLinks as $link}
		<tr class="cell{cycle name='adminlinks' values='0,1'}">
			<td>
				{$link}
			</td>
		</tr>
		{/foreach}
	</table>