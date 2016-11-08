	<table class="outline margin threadreview">
		<tr class="header1">
			<th colspan=2>Thread review</th>
		</tr>
		{foreach $review as $post}
		<tr>
			<td class="cell2 side">
				{$post.userlink}<br>
				<span class="smallFonts">Posts: {$post.posts}</span>
			</td>
			<td class="cell{cycle values='0,1'} post">
				<button style="float:right;" onclick="insertQuote({$post.id});">Quote</button>
				{$post.contents}
			</td>
		</tr>
		{/foreach}
	</table>
