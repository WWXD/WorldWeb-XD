	<table class="outline margin form form_settings">
	{foreach $settingfields as $cat=>$settings}
		<tr class="header1">
			<th{if !$htmlfield} colspan=2{/if}>
				{if $cat}{$cat}{else}Settings{/if}
			</th>
		</tr>
		{foreach $settings as $setting}
		
			<tr class="cell{cycle values='0,1'}">
			
			{if $htmlfield}
				<td>
					{$setting.field}
				</td>
			{else}
				<td class="cell2 center" style="width: 20%;">
					{$setting.name}
				</td>
				<td>
					{$setting.field}
				</td>
			{/if}
			
			</tr>
			
		{/foreach}
	{/foreach}
	
		<tr class="header0">
			<th{if !$htmlfield} colspan=2{/if}>&nbsp;</th>
		</tr>
		<tr class="cell2">
			{if !$htmlfield}<td></td>{/if}
			<td>
				{$fields.btnSaveExit}
				{$fields.btnSave}
			</td>
		</tr>
	
	</table>