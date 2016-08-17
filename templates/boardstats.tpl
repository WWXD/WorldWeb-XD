	<table class="outline margin boardstats">
		<tr class="cell2 center" style="overflow: auto;">
			<td class="smallFonts">
				<div style="float: left; width: 33%;">&nbsp;<br>&nbsp;</div>
				<div style="float: right; width: 33%; text-align: right;">
				{if $stats.numUsers}
					{plural num=$stats.numUsers what='registered user'}, {$stats.numActive} active ({$stats.pctActive}%)<br>
					Newest user: {$stats.lastUserLink}
				{else}
					No registered users<br>
					&nbsp;
				{/if}
				</div>
				<div class="center">
					{plural num=$stats.numThreads what='thread'} and {plural num=$stats.numPosts what='post'} total<br>
					{plural num=$stats.newToday what='post'} today, {$stats.newLastHour} last hour
				</div>
			</td>
		</tr>
	</table>
