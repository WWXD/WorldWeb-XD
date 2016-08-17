	<table class="outline margin pmlist">
		<tr class="header1">
			<th>&nbsp;</th>
			<th>Title</th>
			<th>{if $inbox}From{else}To{/if}</th>
			<th style="min-width:120px;">Date</th>
			<th style="width:2px;">{$deleteCheckAll}</th>
		</tr>
		
		{foreach $pms as $pm}
		
		<tr class="cell{cycle values='0,1'}">
			<td class="cell2 newMarker">{$pm.newIcon}</td>
			<td>{$pm.link}</td>
			<td class="center">{$pm.userlink}</td>
			<td class="center">{$pm.formattedDate}</td>
			<td class="center">{$pm.deleteCheck}</td>
		</tr>
		
		{foreachelse}
		
		<tr class="cell1">
			<td class="center" colspan=5>
				No messages.
			</td>
		</tr>
		
		{/foreach}
		
		<tr class="header0">
			<th colspan=5>
				<span style="float: right;">{$deleteCheckedLink}</span>
			</th>
		</tr>
	</table>
