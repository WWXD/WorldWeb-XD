	<table class="outline margin form form_lostpass">
		<tr class="header1">
			<th colspan=2>
				Request password reset
			</th>
		</tr>
		<tr>
			<td class="cell2 center" style="width:20%;">
				User name
			</td>
			<td class="cell0">
				{$fields.username}
			</td>
		</tr>
		<tr>
			<td class="cell2 center">
				Email address
			</td>
			<td class="cell1">
				{$fields.email} | Confirm: {$fields.email2}
			</td>
		</tr>
		<tr class="cell2">
			<td></td>
			<td>
				{$fields.btnSendReset}
			</td>
		</tr>
		<tr>
			<td class="cell1 smallFonts" colspan=2>
				If automated password reset fails, you can try contacting one of the board's administrators.
			</td>
		</tr>
	</table>
