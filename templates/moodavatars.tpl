	<table class="outline margin form moodavatars">
		<tr class="header1">
			<th colspan=2>Mood avatars</th>
		</tr>
		
		{foreach $avatars as $avatar}
		<tr class="cell{cycle values='0,1'}">
			<td class="cell2 center" style="width:200px; height:200px;">
				{$avatar.avatar}
			</td>
			<td>
				{$avatar.field}
			</td>
		</tr>
		{foreachelse}
		<tr class="cell1">
			<td colspan=2>No avatars</td>
		</tr>
		{/foreach}
		
		<tr class="header1">
			<th colspan=2>Add new</th>
		</tr>
		<tr class="cell2">
			<td></td>
			<td>
				{$newField}
			</td>
		</tr>
		
	</table>
