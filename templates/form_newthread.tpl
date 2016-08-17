	<table class="outline margin form form_newthread">
		<tr class="header1">
			<th colspan=2>New thread</th>
		</tr>
		<tr class="cell0">
			<td class="cell2 center" style="width: 20%;">
				Title
			</td>
			<td id="threadTitleContainer">
				{$fields.title}
			</td>
		</tr>
		<tr class="cell1">
			<td class="cell2 center">
				Icon
			</td>
			<td class="threadIcons">
				{$fields.icon}
			</td>
		</tr>
		<tr class="header0"><th colspan=2 style="height:5px;"></th></tr>
		<tr class="cell0 pollModeOff">
			<td class="cell2"></td>
			<td>
				{$fields.btnAddPoll}
			</td>
		</tr>
		<tr class="cell0 pollModeOn">
			<td class="cell2 center">Poll question</td>
			<td>
				{$fields.pollQuestion}
			</td>
		</tr>
		<tr class="cell1 pollModeOn">
			<td class="cell2 center">Poll options</td>
			<td>
				{$fields.pollOptions}
			</td>
		</tr>
		<tr class="cell0 pollModeOn">
			<td class="cell2 center"></td>
			<td>
				{$fields.pollMultivote}
			</td>
		</tr>
		<tr class="cell1 pollModeOn">
			<td class="cell2"></td>
			<td>
				{$fields.btnRemovePoll}
			</td>
		</tr>
		<tr class="header0"><th colspan=2 style="height:5px;"></th></tr>
		<tr class="cell0">
			<td class="cell2 center">
				Post
			</td>
			<td>
				{$fields.text}
			</td>
		</tr>
		<tr class="cell2">
			<td></td>
			<td>
				{$fields.btnPost}
				{$fields.btnPreview}
				{$fields.mood}
				{$fields.nopl}
				{$fields.nosm}
				{$fields.lock}
				{$fields.stick}
			</td>
		</tr>
	</table>
