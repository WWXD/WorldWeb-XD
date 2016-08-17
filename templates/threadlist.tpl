	{if $pagelinks}<div class="smallFonts pages">Pages: {$pagelinks}</div>{/if}
	
	<table class="outline margin threadlist">
		<tr class="header1">
			<th>&nbsp;</th>
			<th style="width:16px;">&nbsp;</th>
			<th style="width:60%;">Title</th>
			{if $showforum}<th>Forum</th>{/if}
			<th>Started by</th>
			<th>Replies</th>
			<th>Views</th>
			<th style="min-width:150px; width:15%;">Last post</th>
		</tr>
		{foreach $threads as $thread}
		{if $dostickies && !$thread@first && $laststicky != $thread.sticky}
		<tr class="header0"><th colspan={if $showforum}8{else}7{/if} style="height:5px;"></th></tr>
		{/if}
		{$laststicky=$thread.sticky}
		<tr class="cell{if $dostickies && $thread.sticky}2{elseif $thread@index is odd}1{else}0{/if}">
			<td class="cell2 newMarker">{$thread.new}</td>
			<td class="threadIcon" style="border-right:0px none;">{$thread.icon}</td>
			<td style="border-left:0px none;">
				{$thread.gotonew}
				{$thread.poll}
				{$thread.link}
				{if $thread.pagelinks} <small>[{$thread.pagelinks}]</small>{/if}
			</td>
			{if $showforum}<td class="center">{$thread.forumlink}</td>{/if}
			<td class="center">{$thread.startuser}</td>
			<td class="center">{$thread.replies}</td>
			<td class="center">{$thread.views}</td>
			<td class="center smallFonts">
				{$thread.lastpostdate}<br>
				by {$thread.lastpostuser} <a href="{$thread.lastpostlink}">&raquo;</a>
			</td>
		</tr>
		{/foreach}
	</table>
	
	{if $pagelinks}<div class="smallFonts pages">Pages: {$pagelinks}</div>{/if}
