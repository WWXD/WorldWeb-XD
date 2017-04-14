	<table class="outline margin poll">
		<tr class="header1">
			<th colspan=2>{$poll.question}</th>
		</tr>
		{foreach $poll.options as $option}
		<tr class="cell{cycle values='0,1'}">
			<td style="width: 25%;">
				{$option.label}
			</td>
			<td>
				<div class="pollbarContainer">
					{if $option.votes}
					<div class="pollbar" style="background-color: {$option.color}; width: {$option.percent}%;">
						&nbsp;{$option.votes} ({$option.percent}%)
					</div>
					{else}
					&nbsp;0 (0%)
					{/if}
				</div>
			</td>
		</tr>
		{/foreach}
		<tr class="cell{cycle values='0,1'}">
			<td colspan=2 class="smallFonts">
			{if $poll.multivote}
				Multiple voting is allowed.
			{else}
				Multiple voting is not allowed.
			{/if}
			{if $poll.voters == 1}
				1 user has voted so far.
			{else}
				{$poll.voters} users have voted so far.
			{/if}
			{if $poll.multivote}
				Total votes: {$poll.votes}.
			{/if}
			</td>
		</tr>
	</table>
