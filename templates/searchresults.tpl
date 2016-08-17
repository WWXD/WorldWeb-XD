	{if $pagelinks}<div class="smallFonts pages">Pages: {$pagelinks}</div>{/if}
	
	<table class="outline margin threadlist">
		<tr class="header1">
			<th colspan=3>Search results &mdash; {$resultstext}</th>
		</tr>
		{if $nresults>0}
		<tr class="header0">
			<th>Result</th>
			<th>Posted by</th>
			<th>Date</th>
		</tr>
		
		{foreach $results as $result}
		
		<tr class="cell{cycle values='0,1'}">
			<td>
				{$result.link}<br>
				{$result.description}
			</td>
			<td class="center">{$result.user}</td>
			<td class="center">{$result.formattedDate}</td>
		</tr>
		
		{/foreach}
		
		{/if}
	</table>
	
	{if $pagelinks}<div class="smallFonts pages">Pages: {$pagelinks}</div>{/if}
