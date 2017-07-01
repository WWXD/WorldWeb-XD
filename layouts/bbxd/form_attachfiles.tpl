	<table class="outline margin form form_attachfiles">
		<tr class="header1">
			<th>Attach files</th>
		</tr>
		
		{foreach $files as $file}
		<tr class="cell{cycle values='0,1'}">
			<td>
				{$file}
			</td>
		</tr>
		{/foreach}
		
		<tr class="cell{cycle values='0,1'}">
			<td>
				{$fields.newFile}
			</td>
		</tr>
		
		<tr class="cell2">
			<td>
				{$fields.btnSave}
				Maximum file size is {$fileCap}.
			</td>
		</tr>
		
	</table>