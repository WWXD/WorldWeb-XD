	<table class="outline margin grouplist">
		<tr class="header1"><th>Groups</th></tr>
		<tr class="cell1">
			<td class="center">
			{foreach $groups as $g}
				{if !($g@first)}
				 - 
				{/if}
				{$g}
			{/foreach}
			</td>
		</td>
	</table>
