	{foreach $categories as $cat}
	<table class="outline margin forumlist">
		<tr class="header1">
			<th>&nbsp;</th>
			<th>{$cat.name}</th>
			<th style="width: 75px;">Threads</th>
			<th style="width: 50px;">Posts</th>
			<th style="min-width:150px; width:15%;">Last post</th>
		</tr>
		{foreach $cat.forums as $forum}
		<tr class="cell1">
			<td class="cell2 newMarker">{$forum.new}</td>
			<td>
				<h4{if $forum.ignored} class="ignored"{/if}>{$forum.link}</h4>
				<span class="smallFonts{if $forum.ignored} ignored{/if}">
					{$forum.description}
					{if $forum.localmods}<br>Moderated by: {$forum.localmods}{/if}
					{if $forum.subforums}<br>Subforums: {$forum.subforums}{/if}
				</span>
			</td>
			<td class="center cell2"><span{if $forum.ignored} class="ignored"{/if}>{$forum.threads}</span></td>
			<td class="center cell2"><span{if $forum.ignored} class="ignored"{/if}>{$forum.posts}</span></td>
			<td class="center smallFonts">
				<span{if $forum.ignored} class="ignored"{/if}>
				{if $forum.lastpostdate}
					{$forum.lastpostdate}<br>
					by {$forum.lastpostuser} <a href="{$forum.lastpostlink}">&raquo;</a>
				{else}
					&mdash;
				{/if}
				</span>
			</td>
		</tr>
		{/foreach}
	</table>
	{/foreach}
