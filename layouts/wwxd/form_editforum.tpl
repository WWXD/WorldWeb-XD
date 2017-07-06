	<table class="outline margin form form_editforum">
		<tr class="header1">
			<th colspan="2">{$formtitle}</th>
		</tr>
		<tr class="cell1">
			<td class="cell2 center" style="width: 20%;">Title</td>
			<td>
				{$fields.title}
			</td>
		</tr>
		<tr class="cell0">
			<td class="cell2 center">Description</td>
			<td>
				{$fields.description}<br>
				<small>HTML allowed.</small>
			</td>
		</tr>
		<tr class="cell1">
			<td class="cell2 center">Parent</td>
			<td>
				{$fields.parent}
			</td>
		</tr>
		<tr class="cell0">
			<td class="cell2 center">Priority</td>
			<td>
				{$fields.order}<br>
				<small>Forums are sorted by their priority value, and then by their ID.</small>
			</td>
		</tr>
		<tr class="cell1">
			<td class="cell2 center">Redirect</td>
			<td>
				{$fields.redirect}<br>
				<small>Enter an URL to make a redirect forum leading to that URL. Leave blank for no redirect.</small>
			</td>
		</tr>
		<tr class="cell0">
			<td class="cell2 center"></td>
			<td>
				{$fields.hidden}
				{$fields.offtopic}
			</td>
		</tr>
		
		<tr class="header0"><th colspan=2>Permissions</th></tr>
		
		{foreach $groups as $group}
		<tr class="cell{cycle values='0,1'}">
			<td class="cell2 center">
				{$group.name}
			</td>
			<td>
				{$group.permFields}
			</td>
		</tr>
		{/foreach}
		
		<tr class="header0"><th colspan=2>&nbsp;</th></tr>
		<tr class="cell2">
			<td></td>
			<td>
				{$fields.btnSave}
				{$fields.btnDelete}
				<span id="status"></span>
				<br>
				<small>{$delMessage}</small>
			</td>
		</tr>
	</table>
