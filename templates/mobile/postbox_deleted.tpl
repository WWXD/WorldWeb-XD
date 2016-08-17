	<table class="outline margin" id="post{$post.id}">
		<tr class="cell0">
			<td class="right">
				<span style="float:left;">
					{$post.userlink} - <small>deleted</small>
				</span>
				<small>
					<ul class="pipemenu">
						{if $post.links.undelete}<li>{$post.links.undelete}{/if}
						{if $post.links.view}<li>{$post.links.view}{/if}
						<li>#{$post.id}
						{if $post.ip}<li>{$post.ip}{/if}
					</ul>
				</small>
			</td>
		</tr>
	</table>
