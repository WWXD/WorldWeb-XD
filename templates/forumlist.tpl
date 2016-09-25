	{foreach $categories as $cat}
	<table class="outline margin forumlist">
		<tr class="header1">
			<th>{$cat.name}</th>
		</tr>
		{foreach $cat.forums as $forum}
		<tr class="cell1">
			<td>
				<h4{if $forum.ignored} class="ignored"{/if}>{$forum.link}</h4>
				<span class="smallFonts{if $forum.ignored} ignored{/if}">
					{$forum.description}
					{if $forum.localmods}<br>Moderated by: {$forum.localmods}{/if}
					{if $forum.subforums}<br>Subforums: {$forum.subforums}{/if}
				</span>
			</td>
			</tr>
		{/foreach}
	</table>
	{/foreach}
