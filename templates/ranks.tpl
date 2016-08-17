	{if count($ranksets) > 1}
	<table class="outline margin ranksets">
		<tr class="header1">
			<th colspan=2>
				Ranksets
			</th>
		</tr>
		<tr class="cell0">
			<td>
				<ul class="pipemenu">
				{foreach $ranksets as $rset}
					<li>{$rset}
				{/foreach}
				</ul>
			</td>
	</table>
	{/if}
	
	<table class="outline margin ranklist">
		<tr class="header1">
			<th>Rank</th>
			<th>Posts</th>
			<th colspan=2>Users</th>
		</tr>
		
		{foreach $ranks as $rank}
		<tr class="cell{cycle values='0,1'}">
			<td class="cell2">
				{$rank.rank}
			</td>
			<td class="center">
				{$rank.posts}
			</td>
			<td class="center">
				{$rank.numUsers}
			</td>
			<td>
				{$rank.users}
			</td>
		</tr>
		{/foreach}
		
	</table>
