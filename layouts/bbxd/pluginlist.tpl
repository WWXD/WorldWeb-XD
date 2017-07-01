	<table class="outline margin form pluginlist">
		<tr class="header1">
			<th colspan=2>Enabled plugins</th>
		</tr>
		
		{if count($enabledPlugins) > 0}
		
		{foreach $enabledPlugins as $plugin}
		<tr class="cell{cycle values='0,1'}">
			<td>
				<strong>{$plugin.name}</strong> {if $plugin.author}(by {$plugin.author}){/if}<br>
				<span class="smallFonts">{$plugin.description}</span>
			</td>
			<td class="cell2">
				{$plugin.actions}
			</td>
		</tr>
		{/foreach}
		
		{else}
		<tr class="cell2">
			<td colspan=2>No plugins enabled.</td>
		</tr>
		{/if}
		
		<tr class="header1">
			<th colspan=2>Disabled plugins</th>
		</tr>
		
		{if count($disabledPlugins) > 0}
		
		{foreach $disabledPlugins as $plugin}
		<tr class="cell{cycle values='0,1'}">
			<td>
				<strong>{$plugin.name}</strong> {if $plugin.author}(by {$plugin.author}){/if}<br>
				<span class="smallFonts">{$plugin.description}</span>
			</td>
			<td class="cell2">
				{$plugin.actions}
			</td>
		</tr>
		{/foreach}
		
		{else}
		<tr class="cell2">
			<td colspan=2>No plugins disabled.</td>
		</tr>
		{/if}
	</table>