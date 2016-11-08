	{if $pagelinks}<div class="smallFonts pages">Pages: {$pagelinks}</div>{/if}
	
	<table class="outline margin threadlist">
		<tr class="header1">
			<th>Threads</th>
		</tr>
		{foreach $threads as $thread}
		{if $dostickies && !$thread@first && $laststicky != $thread.sticky}
		<tr class="header0"><th style="height:5px;"></th></tr>
		{/if}
		{$laststicky=$thread.sticky}
		<tr class="cell{if $dostickies && $thread.sticky}2{elseif $thread@index is odd}1{else}0{/if}">
			<td>
				{$thread.new}
				{$thread.poll}
				{$thread.link}
				{if $thread.pagelinks} <small>[{$thread.pagelinks}]</small>{/if}
				<br>
				<small>By {$thread.startuser}
				{if $showforum} in {$thread.forumlink}{/if}
				&mdash; {plural num=$thread.replies what='reply'}<br>
				<a href="{$thread.lastpostlink}">Last post</a> by {$thread.lastpostuser} on {$thread.lastpostdate}</small>
			</td>
		</tr>
		{/foreach}
	</table>
	
	{if $pagelinks}<div class="smallFonts pages">Pages: {$pagelinks}</div>{/if}
