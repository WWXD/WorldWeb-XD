	<table class="outline margin form form_editcategory">
		<tr class="header1">
			<th colspan="2">{$formtitle}</th>
		</tr>
		<tr class="cell1">
			<td class="cell2 center" style="width: 20%;">Name</td>
			<td>
				{$fields.name}
			</td>
		</tr>
		<tr class="cell0">
			<td class="cell2 center">Priority</td>
			<td>
				{$fields.order}<br>
				<small>Categories are sorted by their priority value, and then by their ID.</small>
			</td>
		</tr>
		{if $fields.board}
		<tr class="cell1">
			<td class="cell2 center">Board</td>
			<td>
				{$fields.board}
			</td>
		</tr>
		{/if}
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
