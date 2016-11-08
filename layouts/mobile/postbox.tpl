	<table class="outline margin" id="post{$post.id}">
		<tr class="cell0">
			<td>
				{$post.userlink} -
				<small>
					<span id="meta_{$post.id}">
					{if $post.type == $smarty.const.POST_SAMPLE}
						Preview
					{else}
						{if $post.type == $smarty.const.POST_PM}Sent{else}Posted{/if} on {$post.formattedDate}
						{if $post.threadlink} in {$post.threadlink}{/if}
						{if $post.revdetail} ({$post.revdetail}){/if}
					{/if}
					</span>
					<span style="text-align:left; display: none;" id="dyna_{$post.id}">
						blarg
					</span>
				</small>
			</td>
		</tr>
		<tr class="cell1">
			<td id="post_{$post.id}">
				{$post.contents}
			</td>
		</tr>
		{if count($post.links)>0}
		<tr class="cell0">
			<td class="right">
				<small>
					<ul class="pipemenu">
					{if $post.type == $smarty.const.POST_NORMAL}
						<li><a href="{actionLink page='post' id=$post.id}">Link</a>
						{if $post.links.quote}<li>{$post.links.quote}{/if}
						{if $post.links.edit}<li>{$post.links.edit}{/if}
						{if $post.links.delete}<li>{$post.links.delete}{/if}
						{if $post.links.report}<li>{$post.links.report}{/if}
						{foreach $post.links.extra as $link}
							<li>{$link}
						{/foreach}
					{else if $post.type == $smarty.const.POST_DELETED_SNOOP}
						<li>Post deleted
						{if $post.links.undelete}<li>{$post.links.undelete}{/if}
						{if $post.links.close}<li>{$post.links.close}{/if}
					{/if}
						{if $post.id}<li>#{$post.id}{/if}
						{if $post.ip}<li>{$post.ip}{/if}
					</ul>
				</small>
			</td>
		</tr>
		{/if}
	</table>
