	<table class="outline margin form form_editthread">
		<tr class="header1">
			<th colspan=2>Edit thread</th>
		</tr>
		
		{if $canRename}
		<tr class="cell{cycle values='0,1'}">
			<td class="cell2 center" style="width: 20%;">
				Title
			</td>
			<td id="threadTitleContainer">
				{$fields.title}
			</td>
		</tr>
		<tr class="cell{cycle values='0,1'}">
			<td class="cell2 center">
				Icon
			</td>
			<td class="threadIcons">
				{$fields.icon}
			</td>
		</tr>
		{/if}
		
		{if $canClose}
		<tr class="cell{cycle values='0,1'}">
			<td class="cell2"></td>
			<td>
				{$fields.closed}
			</td>
		</tr>
		{/if}
		
		{if $canStick}
		<tr class="cell{cycle values='0,1'}">
			<td class="cell2"></td>
			<td>
				{$fields.sticky}
			</td>
		</tr>
		{/if}
		
		{if $canMove}
		<tr class="cell{cycle values='0,1'}">
			<td class="cell2 center">
				Forum
			</td>
			<td>
				{$fields.forum}
			</td>
		</tr>
		{/if}
		
		<tr class="cell2">
			<td></td>
			<td>
				{$fields.btnEditThread}
			</td>
		</tr>
		
	</table>
