	<table class="outline margin newspost">
		<tr class="header1">
			<th style="text-align:left!important;">
				<span style='float:right;text-align:right;font-weight:normal;'>
					<ul class="pipemenu">
						{if $post.links.edit}<li>{$post.links.edit}{/if}
						{if $post.links.delete}<li>{$post.links.delete}{/if}
					</ul>
				</span>
				<span style='font-size:125%;'>
					{$post.title}
				</span>
				<br>
				<span style="font-weight:normal;font-size:97%;">
					Posted on {$post.formattedDate} by {$post.userlink}
				</span>
			</th>
		</tr>
		<tr class="cell0">
			<td style="padding:10px;">
				{$post.text}
			</td>
		</tr>
		<tr class="cell1">
			<td>
				{$post.comments}. {$post.replylink}
			</td>
		</tr>
	</table>
