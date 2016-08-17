	{foreach $categories as $cat}
	<table class="outline margin forumlist">
		<tr class="header1">
			<th>{$cat.name}</th>
		</tr>
		{foreach $cat.forums as $forum}
		<tr class="cell1">
			<td>
				{$forum.new} <span{if $forum.ignored} class="ignored"{/if}>{$forum.link}</span><br>
				<span class="smallFonts{if $forum.ignored} ignored{/if}">
					{$forum.description}<br>
					{if $forum.lastpostdate}
						<a href="{$forum.lastpostlink}">Last post</a> by {$forum.lastpostuser} on {$forum.lastpostdate}
					{else}
						No posts
					{/if}
					{if $forum.subforums}<br>Subforums: {$forum.subforums}{/if}
				</span>
			</td>
		</tr>
		{/foreach}
	</table>
	{/foreach}
