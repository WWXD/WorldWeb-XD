	<table class="outline margin form form_sendprivate">
		<tr class="header1">
			<th colspan=2>Send PM</th>
		</tr>
		<tr class="cell0">
			<td class="cell2 center" style="width: 20%;">
				To
			</td>
			<td>
				{$fields.to}<br>
				<small>You can specify up to {$maxRecipients} recipients. Separate their names with a semicolon.</small>
			</td>
		</tr>
		<tr class="cell0">
			<td class="cell2 center">
				Title
			</td>
			<td>
				{$fields.title}
			</td>
		</tr>
		<tr class="cell0">
			<td class="cell2 center">
				Message
			</td>
			<td>
				{$fields.text}
			</td>
		</tr>
		<tr class="cell2">
			<td></td>
			<td>
				{$fields.btnSend}
				{$fields.btnPreview}
				{$fields.btnSaveDraft}
				{if $draftMode}{$fields.btnDeleteDraft}{/if}
			</td>
		</tr>
	</table>
