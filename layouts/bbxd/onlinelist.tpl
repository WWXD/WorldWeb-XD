	<div class="smallFonts margin">
		Show visitors from within:
		<ul class="pipemenu">
		{foreach $timelinks as $link}
			<li>{$link}
		{/foreach}
		</ul>
	</div>
	
	<table class="outline margin onlineusers">
		<tr class="header1">
			<th style="width:30px;">#</th>
			<th>Name</th>
			<th style="width:150px;">Last post</th>
			<th style="width:150px;">Last view</th>
			<th>URL</th>
			{if $showIPs}<th style="width:150px;">IP</th>{/if}
		</tr>
		
		{foreach $users as $user}
		<tr class="cell{cycle values='0,1'}">
			<td class="cell2 center">
				{$user.num}
			</td>
			<td>
				{$user.link}
			</td>
			<td class="center">
				{$user.lastPost}
			</td>
			<td class="center">
				{$user.lastView}
			</td>
			<td>
				{$user.lastURL}
			</td>
			{if $showIPs}
			<td class="center">
				{$user.ip}
			</td>
			{/if}
		</tr>
		{foreachelse}
		<tr class="cell1">
			<td colspan={if $showIPs}6{else}5{/if}>No users</td>
		</tr>
		{/foreach}
	</table>
	
	<table class="outline margin onlineguests">
		<tr class="header1">
			<th style="width:30px;">#</th>
			{if $showIPs}<th>User agent</th>{/if}
			<th style="width:150px;">Last view</th>
			<th>URL</th>
			{if $showIPs}<th style="width:150px;">IP</th>{/if}
		</tr>
		
		<tr class="header0">
			<th colspan={if $showIPs}5{else}3{/if}>Guests</th>
		</tr>
		{foreach $guests as $user}
		<tr class="cell{cycle values='0,1'}">
			<td class="cell2 center">
				{$user.num}
			</td>
			{if $showIPs}
			<td>
				{$user.userAgent}
			</td>
			{/if}
			<td class="center">
				{$user.lastView}
			</td>
			<td>
				{$user.lastURL}
			</td>
			{if $showIPs}
			<td class="center">
				{$user.ip}
			</td>
			{/if}
		</tr>
		{foreachelse}
		<tr class="cell1">
			<td colspan={if $showIPs}5{else}3{/if}>No guests</td>
		</tr>
		{/foreach}
		
		<tr class="header0">
			<th colspan={if $showIPs}5{else}3{/if}>Bots</th>
		</tr>
		{foreach $bots as $user}
		<tr class="cell{cycle values='0,1'}">
			<td class="cell2 center">
				{$user.num}
			</td>
			{if $showIPs}
			<td>
				{$user.userAgent}
			</td>
			{/if}
			<td class="center">
				{$user.lastView}
			</td>
			<td>
				{$user.lastURL}
			</td>
			{if $showIPs}
			<td class="center">
				{$user.ip}
			</td>
			{/if}
		</tr>
		{foreachelse}
		<tr class="cell1">
			<td colspan={if $showIPs}5{else}3{/if}>No bots</td>
		</tr>
		{/foreach}
	</table>
