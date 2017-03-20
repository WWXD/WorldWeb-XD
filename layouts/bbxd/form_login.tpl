	<table class="outline margin form form_login">
		<tr class="header1">
			<th colspan=2>
				Log in
			</th>
		</tr>
		<tr>
			<td class="cell2 center" style="width:20%;">
				User name or Email
			</td>
			<td class="cell0">
				{$fields.username}
			</td>
		</tr>
		<tr>
			<td class="cell2 center">
				Password
			</td>
			<td class="cell1">
				{$fields.password}
			</td>
		</tr>
		<tr>
			<td class="cell2"></td>
			<td class="cell1">
				{$fields.session}
			</td>
		</tr>
		<tr class="cell2">
			<td></td>
			<td>
				{$fields.btnLogin}
				{$fields.btnForgotPass}
				<input type="hidden" name="action" value="login">
			</td>
		</tr>
	</table>
